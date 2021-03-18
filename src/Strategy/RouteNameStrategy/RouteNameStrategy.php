<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy;

use Ctw\Middleware\PageCacheMiddleware\Strategy\StrategyInterface;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

class RouteNameStrategy extends AbstractStrategy implements StrategyInterface
{
    public function shouldCache(ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class);

        if (!$routeResult instanceof RouteResult) {
            return false;
        }

        $matchedRoute = $routeResult->getMatchedRoute();

        if (!$matchedRoute instanceof Route) {
            return false;
        }

        return in_array($matchedRoute->getName(), $this->getNames());
    }
}
