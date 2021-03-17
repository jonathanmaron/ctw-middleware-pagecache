<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGeneratorFactory;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddlewareFactory;
use CtwTest\Middleware\PageCacheMiddleware\TestAsset\TestHandler;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseInterface;

// @todo Complete these tests.

class PageCacheMiddlewareTest extends AbstractCase
{
    public function testPageCacheMiddleware(): void
    {
        $content     = (string) file_get_contents(__DIR__ . '/TestAsset/test_input.htm');
        $contentType = 'text/html';

        $request = Factory::createServerRequest('GET', '/');

        //$request = $request->withAttribute(RouteResult::class, $this->getRouteResult());

        $stack    = [
            $this->getInstance(),
            function () use ($content, $contentType): ResponseInterface {
                $response = Factory::createResponse();
                $body     = Factory::getStreamFactory()->createStream($content);

                return $response->withHeader('Content-Type', $contentType)->withBody($body);
            },
        ];
        $response = Dispatcher::run($stack, $request);

        //dump($response->getHeaders());
        //dump($response->getBody()->getContents());

        // $this->assertEquals('[..]', $actual);

        $this->assertTrue(true);
    }

    private function getInstance(): PageCacheMiddleware
    {
        $container = new ServiceManager();

        $config = [
            PageCacheMiddleware::class => [
                'enabled'      => true,
                'id_generator' => FullUriIdGeneratorFactory::class,
                'handlers'     => [
                    TestHandler::class,
                ],
            ],
        ];

        $container->setService('config', $config);
        $container->setService('tism_cache_storage_adapter', $this->getStorageAdapter());

        $factory = new PageCacheMiddlewareFactory();

        return $factory->__invoke($container);
    }
    /*
    private function getRouteResult(): RouteResult
    {
        return RouteResult::fromRoute($route1, $params);
    }
    */
}
