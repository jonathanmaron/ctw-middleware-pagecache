<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\ConfigProvider;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddlewareFactory;

class ConfigProviderTest extends AbstractCase
{
    public function testConfigProvider(): void
    {
        $configProvider = new ConfigProvider();

        $expected = [
            'dependencies' => [
                'factories' => [
                    PageCacheMiddleware::class => PageCacheMiddlewareFactory::class,
                ],
            ],
        ];

        self::assertSame($expected, $configProvider->__invoke());
    }
}
