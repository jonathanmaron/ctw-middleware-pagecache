<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;
use Ctw\Middleware\PageCacheMiddleware\Strategy\StrategyInterface;
use Laminas\Cache\Storage\Adapter\AbstractAdapter as StorageAdapter;
use Psr\Container\ContainerInterface;

class PageCacheMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): PageCacheMiddleware
    {
        $config = $container->get('config');
        assert(is_array($config));

        $config = $config[PageCacheMiddleware::class];

        $enabled        = $this->getEnabled($config);
        $storageAdapter = $this->getStorageAdapter($container);
        $idGenerator    = $this->getIdGenerator($container, $config);
        $strategy       = $this->getStrategy($container, $config);

        $middleware = new PageCacheMiddleware();

        $middleware->setEnabled($enabled);
        $middleware->setStorageAdapter($storageAdapter);
        $middleware->setIdGenerator($idGenerator);
        $middleware->setStrategy($strategy);

        return $middleware;
    }

    private function getEnabled(array $config): bool
    {
        return $config['enabled'];
    }

    private function getStorageAdapter(ContainerInterface $container): StorageAdapter
    {
        $storageAdapter = $container->get('ctw_cache_storage_adapter');
        assert($storageAdapter instanceof StorageAdapter);

        return $storageAdapter;
    }

    private function getIdGenerator(ContainerInterface $container, array $config): IdGeneratorInterface
    {
        $factoryName = sprintf('%sFactory', $config['id_generator']);
        $factory     = new $factoryName();
        assert(is_callable($factory));

        return $factory($container);
    }

    private function getStrategy(ContainerInterface $container, array $config): StrategyInterface
    {
        $keys = array_keys($config['strategy']);

        $factoryName = sprintf('%sFactory', array_shift($keys));
        $factory     = new $factoryName();
        assert(is_callable($factory));

        return $factory($container);
    }
}
