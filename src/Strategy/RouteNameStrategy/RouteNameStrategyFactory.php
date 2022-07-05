<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy;

use Ctw\Middleware\PageCacheMiddleware\PageCacheMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RouteNameStrategyFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): RouteNameStrategy
    {
        $config = $container->get('config');
        assert(is_array($config));

        $config = $config[PageCacheMiddleware::class];
        $config = $config['strategy'];
        $config = $config[RouteNameStrategy::class];

        $strategy = new RouteNameStrategy();
        $strategy->setConfig($config);

        return $strategy;
    }
}
