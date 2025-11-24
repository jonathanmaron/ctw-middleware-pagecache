<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy;

use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Psr\Container\ContainerInterface;

class RouteNameStrategyFactory
{
    public function __invoke(ContainerInterface $container): RouteNameStrategy
    {
        $config = $container->get('config');
        assert(is_array($config));

        $middlewareConfig = $config[PageCacheMiddleware::class];
        assert(is_array($middlewareConfig));

        $strategyConfig = $middlewareConfig['strategy'];
        assert(is_array($strategyConfig));

        $routeNameConfig = $strategyConfig[RouteNameStrategy::class];
        assert(is_array($routeNameConfig));

        $strategy = new RouteNameStrategy();
        $strategy->setConfig($routeNameConfig);

        return $strategy;
    }
}
