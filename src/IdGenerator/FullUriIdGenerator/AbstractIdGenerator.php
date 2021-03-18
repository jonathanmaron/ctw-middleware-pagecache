<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\AbstractIdGenerator as ParentAbstractIdGenerator;

class AbstractIdGenerator extends ParentAbstractIdGenerator
{
    /**
     * Using salted params, create a SHA256 hash.
     *
     * @param string $httpHost
     * @param string $serverPort
     * @param string $requestUri
     *
     * @return string
     */
    protected function getHash(string $httpHost, string $serverPort, string $requestUri): string
    {
        $data = sprintf('%s|%s|%s|%s', self::SALT, $httpHost, $serverPort, $requestUri);

        return hash('sha256', $data);
    }
}
