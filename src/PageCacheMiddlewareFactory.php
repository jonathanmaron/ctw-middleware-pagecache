<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

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

            $class = $config['id_generator'];
            if (is_int(strpos($class, 'Factory'))) {
                $factory     = new $class();
                $idGenerator = $factory->__invoke($container);
            } else {
                $idGenerator = new $class();
            }

            $middleware->setIdGenerator($idGenerator);
            $middleware->setStorageAdapter($storageAdapter);
            $middleware->setEnabled($config['enabled']);
            $middleware->setHandlers($config['handlers']);
        }

        return $middleware;
    }
}
