<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGenerator;

class FullUriIdGeneratorTest extends AbstractCase
{
    public function testFullUriIdGenerator(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['HTTP_HOST']   = 'https://www.example.com';
        $_SERVER['SERVER_PORT'] = 443;

        $idGenerator = new FullUriIdGenerator();

        $expected = 'c51b3dc89bd6ee8f395cdb0e974c8af29934f522b594a3b354893cb618bec5fa';
        $actual   = $idGenerator->generate();

        $this->assertSame($expected, $actual);
    }
}
