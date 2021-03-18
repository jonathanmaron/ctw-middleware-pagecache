<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGeneratorFactory;
use Laminas\ServiceManager\ServiceManager;

class RequestUriGeneratorTest extends AbstractCase
{
    public function testRequestUriGenerator(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';

        $container = new ServiceManager();
        $factory   = new RequestUriGeneratorFactory();

        $idGenerator = $factory->__invoke($container);

        $expected = '60950c02cc9958a16a00a7b3fceba7398597b5dec94d29357fb2ab7c9c939496';

        $this->assertSame($expected, $idGenerator->generate());
    }

    public function testRequestUriGeneratorException(): void
    {
        $this->expectException(RuntimeException::class);

        unset($_SERVER['REQUEST_URI']);

        $idGenerator = new RequestUriGenerator();

        $idGenerator->generate();
    }
}
