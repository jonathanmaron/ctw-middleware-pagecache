<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator;

use Psr\Http\Message\ServerRequestInterface;

interface IdGeneratorInterface
{
    /**
     * Return a unique ID that represents the request
     * 
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function generate(ServerRequestInterface $request): string;
}
