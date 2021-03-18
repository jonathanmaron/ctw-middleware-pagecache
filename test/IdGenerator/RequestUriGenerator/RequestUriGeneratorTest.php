<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGenerator;

class RequestUriGeneratorTest extends AbstractCase
{
    public function testRequestUriGenerator(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';

        $idGenerator = new RequestUriGenerator();

        $expected = '60950c02cc9958a16a00a7b3fceba7398597b5dec94d29357fb2ab7c9c939496';
        $actual   = $idGenerator->generate();

        $this->assertSame($expected, $actual);
    }
}
