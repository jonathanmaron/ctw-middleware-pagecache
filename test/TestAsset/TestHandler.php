<?php
declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\TestAsset;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TestHandler implements RequestHandlerInterface
{
    /**
     * @var string
     */
    final public const NAME = 'app::test/test';

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse('<html></html>');
    }
}
