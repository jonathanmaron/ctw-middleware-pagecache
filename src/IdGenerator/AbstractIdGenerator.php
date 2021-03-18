<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator;

abstract class AbstractIdGenerator
{
    /**
     * Emergency invalidation salt.
     *
     * When making substantial changes to this package,
     * all existing cached files can be invalided by changing this value.
     *
     * @var string
     */
    protected const SALT = 'rhi0skgJnnyMvEwxVkSiOZK6wtIcX6lZlGuXRrAu';
}
