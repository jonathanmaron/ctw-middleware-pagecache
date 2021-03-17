<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

class FullUriIdGeneratorTest extends AbstractCase
{
    public function testFullUriIdGenerator(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['HTTP_HOST']   = 'https://www.example.com';
        $_SERVER['SERVER_PORT'] = 443;

        $idGenerator = new FullUriIdGenerator();

        $expected = '08d4c64178cb39c53e96eab01bb2f5208db2beaf26b09895115d1d9d4f5bce1b';
        $actual   = $idGenerator->generate();

        $this->assertSame($expected, $actual);
    }
}
