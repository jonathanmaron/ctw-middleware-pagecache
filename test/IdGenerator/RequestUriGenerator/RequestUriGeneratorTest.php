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

        $expected = 'abc876abc281af806683bef2805b826568b04e786ac354e1c7552571005f3b07';

        self::assertSame($expected, $idGenerator->generate($request));
    }

    public function testRequestUriGeneratorException(): void
    {
        $this->expectException(RuntimeException::class);

        $request     = new ServerRequest([], [], new Uri());
        $idGenerator = new RequestUriGenerator();

        $idGenerator->generate($request);
    }
}
