<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGenerator;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddlewareFactory;
use Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy\RouteNameStrategy;
use CtwTest\Middleware\PageCacheMiddleware\TestAsset\TestHandler;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PageCacheMiddlewareTest extends AbstractCase
{
    /**
     * Test that getEnabled returns the value set via setEnabled.
     */
    public function testSetEnabledThenGetEnabledReturnsAssignedValue(): void
    {
        $middleware = new PageCacheMiddleware();

        self::assertSame($middleware, $middleware->setEnabled(true));
        self::assertTrue($middleware->getEnabled());

        $middleware->setEnabled(false);
        self::assertFalse($middleware->getEnabled());
    }

    /**
     * Test that getStorageAdapter returns the adapter set via setStorageAdapter.
     */
    public function testSetStorageAdapterThenGetStorageAdapterReturnsAssignedAdapter(): void
    {
        $middleware     = new PageCacheMiddleware();
        $storageAdapter = $this->getStorageAdapter();

        self::assertSame($middleware, $middleware->setStorageAdapter($storageAdapter));
        self::assertSame($storageAdapter, $middleware->getStorageAdapter());
    }

    /**
     * Test that getIdGenerator returns the generator set via setIdGenerator.
     */
    public function testSetIdGeneratorThenGetIdGeneratorReturnsAssignedGenerator(): void
    {
        $middleware  = new PageCacheMiddleware();
        $idGenerator = new FullUriIdGenerator();

        self::assertSame($middleware, $middleware->setIdGenerator($idGenerator));
        self::assertSame($idGenerator, $middleware->getIdGenerator());
    }

    /**
     * Test that getStrategy returns the strategy set via setStrategy.
     */
    public function testSetStrategyThenGetStrategyReturnsAssignedStrategy(): void
    {
        $middleware = new PageCacheMiddleware();
        $strategy   = new RouteNameStrategy();

        self::assertSame($middleware, $middleware->setStrategy($strategy));
        self::assertSame($strategy, $middleware->getStrategy());
    }

    /**
     * Test that the handler response is returned unchanged when caching is disabled.
     */
    public function testProcessReturnsHandlerResponseWhenCachingDisabled(): void
    {
        $middleware = $this->getInstance(false);
        $request    = $this->getCacheableRequest();
        $handler    = $this->getHandler();

        $response = $middleware->process($request, $handler);

        self::assertFalse($response->hasHeader('X-Page-Cache'));
    }

    /**
     * Test that the handler response is returned unchanged when the strategy declines caching.
     */
    public function testProcessReturnsHandlerResponseWhenStrategyDeclinesCaching(): void
    {
        $middleware = $this->getInstance(true);
        $request    = new ServerRequest([], [], new Uri('https://www.example.com/uncached'));
        $handler    = $this->getHandler();

        $response = $middleware->process($request, $handler);

        self::assertFalse($response->hasHeader('X-Page-Cache'));
    }

    /**
     * Test that a cache miss invokes the handler and tags the response as a miss.
     */
    public function testProcessReturnsMissResponseWhenCacheIsEmpty(): void
    {
        $middleware = $this->getInstance(true);
        $request    = $this->getCacheableRequest('https://www.example.com/miss/?v=' . uniqid());
        $handler    = $this->getHandler();

        $response = $middleware->process($request, $handler);

        self::assertSame('Miss', $response->getHeaderLine('X-Page-Cache'));
        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that a second request for the same id returns a hit response from the cache.
     */
    public function testProcessReturnsHitResponseWhenItemIsAlreadyCached(): void
    {
        $middleware = $this->getInstance(true);
        $uri        = 'https://www.example.com/hit/?v=' . uniqid();

        $missResponse = $middleware->process($this->getCacheableRequest($uri), $this->getHandler());
        self::assertSame('Miss', $missResponse->getHeaderLine('X-Page-Cache'));

        $hitResponse = $middleware->process($this->getCacheableRequest($uri), $this->getHandler());

        self::assertSame('Hit', $hitResponse->getHeaderLine('X-Page-Cache'));
        self::assertSame(200, $hitResponse->getStatusCode());
    }

    /**
     * Test that a cache hit reconstructs the original response body and headers.
     */
    public function testProcessRestoresOriginalBodyAndHeadersOnCacheHit(): void
    {
        $middleware = $this->getInstance(true);
        $uri        = 'https://www.example.com/restore/?v=' . uniqid();

        $missResponse = $middleware->process($this->getCacheableRequest($uri), $this->getHandler());
        $hitResponse  = $middleware->process($this->getCacheableRequest($uri), $this->getHandler());

        self::assertSame(
            $missResponse->getHeaderLine('Content-Type'),
            $hitResponse->getHeaderLine('Content-Type'),
        );
        self::assertSame((string) $missResponse->getBody(), (string) $hitResponse->getBody());
    }

    /**
     * Test that the configured factory produces a middleware that caches a routed request.
     */
    public function testFactoryBuiltMiddlewareCachesRoutedRequest(): void
    {
        $middleware = $this->getInstance(true);

        $request  = $this->getCacheableRequest('https://www.example.com/factory/?v=' . uniqid());
        $response = $middleware->process($request, $this->getHandler());

        self::assertSame('Miss', $response->getHeaderLine('X-Page-Cache'));
    }

    /**
     * Build a request whose route name matches the configured cacheable names.
     */
    private function getCacheableRequest(string $uri = 'https://www.example.com/'): ServerRequestInterface
    {
        $request     = new ServerRequest([], [], new Uri($uri));
        $route       = new Route('/test', $this->getRouteMiddleware(), null, TestHandler::NAME);
        $routeResult = RouteResult::fromRoute($route);

        return $request->withAttribute(RouteResult::class, $routeResult);
    }

    /**
     * Build a request handler that returns a fresh HTML response.
     */
    private function getHandler(): RequestHandlerInterface
    {
        return new TestHandler();
    }

    /**
     * Create a no-op middleware required by the Mezzio Route value object.
     */
    private function getRouteMiddleware(): MiddlewareInterface
    {
        return new class() implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler,
            ): ResponseInterface {
                return $handler->handle($request);
            }
        };
    }

    /**
     * Build a fully wired middleware via its factory with the supplied enabled flag.
     */
    private function getInstance(bool $enabled): PageCacheMiddleware
    {
        $container = new ServiceManager();

        $config = [
            PageCacheMiddleware::class => [
                'enabled'      => $enabled,
                'id_generator' => FullUriIdGenerator::class,
                'strategy'     => [
                    RouteNameStrategy::class => [
                        'names' => [TestHandler::NAME],
                    ],
                ],
            ],
        ];

        $container->setService('config', $config);
        $container->setService('ctw_cache_storage_adapter', $this->getStorageAdapter());

        $factory = new PageCacheMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
