<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGeneratorFactory;
use Laminas\ServiceManager\ServiceManager;

class FullUriIdGeneratorTest extends AbstractCase
{
    public function testFullUriIdGenerator(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['HTTP_HOST']   = 'https://www.example.com';
        $_SERVER['SERVER_PORT'] = 443;

        $container = new ServiceManager();
        $factory   = new FullUriIdGeneratorFactory();

        $idGenerator = $factory->__invoke($container);

        $expected = '0dc753aea22c9d0a3548b19e888fb5456ef61f0b581e20147b99df1a0966311d';

        $this->assertSame($expected, $idGenerator->generate());
    }
}
