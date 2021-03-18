<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator;

abstract class AbstractIdGenerator
{
    /**
     * Retrieve and normalize server variable.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getServerParam(string $key): string
    {
        if (!isset($_SERVER[$key])) {
            return '';
        }

        $value = (string) $_SERVER[$key];
        $value = trim($value);
        $value = strtolower($value);

        return $value;
    }
}
