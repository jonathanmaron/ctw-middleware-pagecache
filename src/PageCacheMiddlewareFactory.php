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

        $middlewareConfig = $config[PageCacheMiddleware::class];
        assert(is_array($middlewareConfig));

        $enabled        = $this->getEnabled($middlewareConfig);
        $storageAdapter = $this->getStorageAdapter($container);
        $idGenerator    = $this->getIdGenerator($container, $middlewareConfig);
        $strategy       = $this->getStrategy($container, $middlewareConfig);

        $middleware = new PageCacheMiddleware();

        $middleware->setEnabled($enabled);
        $middleware->setStorageAdapter($storageAdapter);
        $middleware->setIdGenerator($idGenerator);
        $middleware->setStrategy($strategy);

        return $middleware;
    }

    private function getEnabled(array $config): bool
    {
        $enabled = $config['enabled'];
        assert(is_bool($enabled));
        return $enabled;
    }

    private function getStorageAdapter(ContainerInterface $container): StorageAdapter
    {
        $storageAdapter = $container->get('ctw_cache_storage_adapter');
        assert($storageAdapter instanceof StorageAdapter);

        return $storageAdapter;
    }

    private function getIdGenerator(ContainerInterface $container, array $config): IdGeneratorInterface
    {
        $idGeneratorClass = $config['id_generator'];
        assert(is_string($idGeneratorClass));
        $factoryName = sprintf('%sFactory', $idGeneratorClass);
        $factory     = new $factoryName();
        assert(is_callable($factory));

        $idGenerator = $factory($container);
        assert($idGenerator instanceof IdGeneratorInterface);
        return $idGenerator;
    }

    private function getStrategy(ContainerInterface $container, array $config): StrategyInterface
    {
        $strategyConfig = $config['strategy'];
        assert(is_array($strategyConfig));
        $keys = array_keys($strategyConfig);

        $strategyClass = array_shift($keys);
        assert(is_string($strategyClass));
        $factoryName = sprintf('%sFactory', $strategyClass);
        $factory     = new $factoryName();
        assert(is_callable($factory));

        $strategy = $factory($container);
        assert($strategy instanceof StrategyInterface);
        return $strategy;
    }
}
