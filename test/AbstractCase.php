<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware;

use Laminas\Cache\Storage\Adapter\Filesystem as CacheStorageAdapter;
use Laminas\Cache\Storage\Plugin\Serializer;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\StorageFactory as LaminasStorageFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractCase extends TestCase
{
    protected function getStorageAdapter(): StorageInterface
    {
        $cacheDir = sys_get_temp_dir() . '/0d28cc3e-c2d3-40bc-9ba5-e182fb92ed53';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        $config = [
            'adapter' => [
                'name'    => CacheStorageAdapter::class,
                'options' => [
                    'ttl'       => 60,
                    'cache_dir' => $cacheDir,
                    'dir_level' => 4,
                    'namespace' => 'default',
                    'readable'  => true,
                    'writable'  => true,
                ],
            ],
            'plugins' => [
                Serializer::class,
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ];

        return LaminasStorageFactory::factory($config);
    }
}
