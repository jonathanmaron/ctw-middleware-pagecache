<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\Strategy;

use Psr\Http\Message\ServerRequestInterface;

interface StrategyInterface
{
    /**
     * Return true, if the resource should be cached, false otherwise.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function shouldCache(ServerRequestInterface $request): bool;
}
