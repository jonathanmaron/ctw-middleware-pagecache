<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware;

use Laminas\Cache\Storage\Adapter\AbstractAdapter as StorageAdapter;
use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Cache\Storage\Plugin\ExceptionHandler;
use Laminas\Cache\Storage\Plugin\Serializer;
use PHPUnit\Framework\TestCase;

abstract class AbstractCase extends TestCase
{
    protected function getStorageAdapter(): StorageAdapter
    {
        $cacheDir = sys_get_temp_dir() . '/0d28cc3e-c2d3-40bc-9ba5-e182fb92ed53';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        $config = [
            'ttl'       => 60,
            'cache_dir' => $cacheDir,
            'dir_level' => 4,
            'namespace' => 'a',
            'readable'  => true,
            'writable'  => true,
        ];

        $storageAdapter = new Filesystem($config);

        $plugin  = new ExceptionHandler();
        $options = $plugin->getOptions();
        $options->setThrowExceptions(false);

        $storageAdapter->addPlugin($plugin);

        $plugin = new Serializer();
        $storageAdapter->addPlugin($plugin);

        return $storageAdapter;
    }
}
