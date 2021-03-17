<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

use Laminas\Cache\Storage\Adapter\Filesystem as CacheFilesystemAdapter;
use Psr\Container\ContainerInterface;

class PageCacheMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): PageCacheMiddleware
    {
        $config = $container->get('config');
        $config = $config[PageCacheMiddleware::class] ?? [];

        $storageAdapter = $container->get('tism_cache_storage_adapter');

        $middleware = new PageCacheMiddleware();

        if (count($config) > 0) {
            $middleware->setConfig($config);
            $middleware->setStorageAdapter($storageAdapter);
        }

        return $middleware;
    }
}
