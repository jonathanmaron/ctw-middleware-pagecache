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

    /**
     * Return a SHA256 hash for the passed $vars
     *
     * @param array<mixed> $vars
     *
     * @return non-empty-string
     */
    protected function getHash(array $vars): string
    {
        $parts = [];

        foreach ($vars as $var) {
            if (in_array($var, [null, '', false], true)) {
                continue;
            }

            if (is_scalar($var) || $var instanceof \Stringable) {
                $parts[] = (string) $var;
            }
        }

        return hash('sha256', implode('|', $parts));
    }
}
