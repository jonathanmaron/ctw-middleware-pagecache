<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGeneratorFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;

class RequestUriGeneratorTest extends AbstractCase
{
    public function testRequestUriGenerator(): void
    {
        $request   = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $container = new ServiceManager();
        $factory   = new RequestUriGeneratorFactory();

        $idGenerator = $factory->__invoke($container);

        $expected = '813bb287f36e285370f6638a10b97e433e83fac565747e769ac98ab240ffe485';

        $this->assertSame($expected, $idGenerator->generate($request));
    }

    public function testRequestUriGeneratorException(): void
    {
        $this->expectException(RuntimeException::class);

        $request     = new ServerRequest([], [], new Uri());
        $idGenerator = new RequestUriGenerator();

        $idGenerator->generate($request);
    }
}
