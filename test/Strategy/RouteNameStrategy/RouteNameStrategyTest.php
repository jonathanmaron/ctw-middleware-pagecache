<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy;

use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy\RouteNameStrategy;
use Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy\RouteNameStrategyFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteNameStrategyTest extends AbstractCase
{
    /**
     * Test that getConfig returns the array assigned through setConfig.
     */
    public function testSetConfigThenGetConfigReturnsAssignedConfig(): void
    {
        $strategy = new RouteNameStrategy();
        $config   = [
            'names' => ['home', 'about'],
        ];

        self::assertSame($strategy, $strategy->setConfig($config));
        self::assertSame($config, $strategy->getConfig());
    }

    /**
     * Test that shouldCache returns false when the request has no route result attribute.
     */
    public function testShouldCacheReturnsFalseWhenNoRouteResultAttributePresent(): void
    {
        $strategy = $this->getStrategy(['home']);
        $request  = new ServerRequest([], [], new Uri('https://www.example.com/'));

        self::assertFalse($strategy->shouldCache($request));
    }

    /**
     * Test that shouldCache returns false when the attribute is not a RouteResult instance.
     */
    public function testShouldCacheReturnsFalseWhenAttributeIsNotRouteResult(): void
    {
        $strategy = $this->getStrategy(['home']);
        $request  = new ServerRequest([], [], new Uri('https://www.example.com/'))
            ->withAttribute(RouteResult::class, 'not-a-route-result');

        self::assertFalse($strategy->shouldCache($request));
    }

    /**
     * Test that shouldCache returns false when routing failed and no route was matched.
     */
    public function testShouldCacheReturnsFalseWhenRoutingFailed(): void
    {
        $strategy    = $this->getStrategy(['home']);
        $routeResult = RouteResult::fromRouteFailure(null);
        $request     = $this->getRequestWithRouteResult($routeResult);

        self::assertFalse($strategy->shouldCache($request));
    }

    /**
     * Test that shouldCache returns false when the matched route name is not configured.
     */
    public function testShouldCacheReturnsFalseWhenMatchedRouteNameNotConfigured(): void
    {
        $strategy    = $this->getStrategy(['home']);
        $route       = new Route('/contact', $this->getRouteMiddleware(), null, 'contact');
        $routeResult = RouteResult::fromRoute($route);
        $request     = $this->getRequestWithRouteResult($routeResult);

        self::assertFalse($strategy->shouldCache($request));
    }

    /**
     * Test that shouldCache returns true when the matched route name is configured.
     */
    public function testShouldCacheReturnsTrueWhenMatchedRouteNameConfigured(): void
    {
        $strategy    = $this->getStrategy(['home', 'about']);
        $route       = new Route('/about', $this->getRouteMiddleware(), null, 'about');
        $routeResult = RouteResult::fromRoute($route);
        $request     = $this->getRequestWithRouteResult($routeResult);

        self::assertTrue($strategy->shouldCache($request));
    }

    /**
     * Test that shouldCache returns false when no route names are configured.
     */
    public function testShouldCacheReturnsFalseWhenConfiguredNamesAreEmpty(): void
    {
        $strategy    = $this->getStrategy([]);
        $route       = new Route('/home', $this->getRouteMiddleware(), null, 'home');
        $routeResult = RouteResult::fromRoute($route);
        $request     = $this->getRequestWithRouteResult($routeResult);

        self::assertFalse($strategy->shouldCache($request));
    }

    /**
     * Test that the factory builds a strategy configured from the container config.
     */
    public function testFactoryBuildsStrategyConfiguredFromContainer(): void
    {
        $container = new ServiceManager();
        $container->setService('config', [
            PageCacheMiddleware::class => [
                'strategy' => [
                    RouteNameStrategy::class => [
                        'names' => ['home'],
                    ],
                ],
            ],
        ]);

        $factory  = new RouteNameStrategyFactory();
        $strategy = $factory->__invoke($container);

        self::assertSame([
            'names' => ['home'],
        ], $strategy->getConfig());
    }

    /**
     * Test that a factory-built strategy caches a request matching its configured route.
     */
    public function testFactoryBuiltStrategyCachesMatchingRoute(): void
    {
        $container = new ServiceManager();
        $container->setService('config', [
            PageCacheMiddleware::class => [
                'strategy' => [
                    RouteNameStrategy::class => [
                        'names' => ['home'],
                    ],
                ],
            ],
        ]);

        $factory     = new RouteNameStrategyFactory();
        $strategy    = $factory->__invoke($container);
        $route       = new Route('/home', $this->getRouteMiddleware(), null, 'home');
        $routeResult = RouteResult::fromRoute($route);
        $request     = $this->getRequestWithRouteResult($routeResult);

        self::assertTrue($strategy->shouldCache($request));
    }

    /**
     * Build a strategy seeded with the supplied cacheable route names.
     *
     * @param list<string> $names
     */
    private function getStrategy(array $names): RouteNameStrategy
    {
        $strategy = new RouteNameStrategy();
        $strategy->setConfig([
            'names' => $names,
        ]);

        return $strategy;
    }

    /**
     * Attach the supplied route result to a fresh server request.
     */
    private function getRequestWithRouteResult(RouteResult $routeResult): ServerRequestInterface
    {
        return new ServerRequest([], [], new Uri('https://www.example.com/'))
            ->withAttribute(RouteResult::class, $routeResult);
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
}
