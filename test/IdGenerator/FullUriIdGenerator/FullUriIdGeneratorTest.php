<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGeneratorFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;

class FullUriIdGeneratorTest extends AbstractCase
{
    public function testFullUriIdGenerator(): void
    {
        $request   = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $container = new ServiceManager();
        $factory   = new FullUriIdGeneratorFactory();

        $idGenerator = $factory->__invoke($container);

        $expected = 'ca53938a1b5e74588f148be54af37ec66cce2e8295cc2bcefab482a3a5ab2d09';

        $this->assertSame($expected, $idGenerator->generate($request));
    }

    public function testFullUriIdGeneratorException(): void
    {
        $this->expectException(RuntimeException::class);

        $request     = new ServerRequest([], [], new Uri());
        $idGenerator = new FullUriIdGenerator();

        $idGenerator->generate($request);
    }
}
