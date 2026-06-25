<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\ConfigProvider;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddlewareFactory;

final class ConfigProviderTest extends AbstractCase
{
    /**
     * Test that invoke returns the full dependencies configuration array.
     */
    public function testInvokeReturnsDependenciesConfiguration(): void
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

    /**
     * Test that getDependencies maps the middleware to its factory.
     */
    public function testGetDependenciesMapsMiddlewareToFactory(): void
    {
        $configProvider = new ConfigProvider();

        $dependencies = $configProvider->getDependencies();

        $expected = [
            'factories' => [
                PageCacheMiddleware::class => PageCacheMiddlewareFactory::class,
            ],
        ];

        self::assertSame($expected, $dependencies);
    }

    /**
     * Test that invoke embeds the result of getDependencies under the dependencies key.
     */
    public function testInvokeEmbedsGetDependenciesResult(): void
    {
        $configProvider = new ConfigProvider();

        $config = $configProvider->__invoke();

        self::assertSame($configProvider->getDependencies(), $config['dependencies']);
    }
}
