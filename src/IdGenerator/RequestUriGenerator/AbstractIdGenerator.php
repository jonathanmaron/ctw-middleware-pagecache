<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\AbstractIdGenerator as ParentAbstractIdGenerator;

class AbstractIdGenerator extends ParentAbstractIdGenerator
{
    /**
     * Using salted param, create a SHA256 hash.
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
