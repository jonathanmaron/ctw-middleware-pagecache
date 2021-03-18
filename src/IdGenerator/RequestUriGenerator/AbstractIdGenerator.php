<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\AbstractIdGenerator as ParentAbstractIdGenerator;

class AbstractIdGenerator extends ParentAbstractIdGenerator
{
    /**
     * Emergency invalidation salt
     *
     * @var string
     */
    private const SALT = 'rhi0skgJnnyMvEwxVkSiOZK6wtIcX6lZlGuXRrAu';

    /**
     * Using the param, create a SHA256 hash.
     *
     * @param string $requestUri
     *
     * @return string
     */
    protected function getHash(string $requestUri): string
    {
        $data = sprintf('%s|%s', self::SALT, $requestUri);

        return hash('sha256', $data);
    }
}
