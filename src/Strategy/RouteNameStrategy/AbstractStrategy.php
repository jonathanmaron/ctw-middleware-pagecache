<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\Strategy\RouteNameStrategy;

use Ctw\Middleware\PageCacheMiddleware\Strategy\AbstractStrategy as ParentAbstractStrategy;

abstract class AbstractStrategy extends ParentAbstractStrategy
{
    protected function getNames(): array
    {
        $config = $this->getConfig();

        return $config['names'];
    }
}
