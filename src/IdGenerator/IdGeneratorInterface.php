<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator;

interface IdGeneratorInterface
{
    /**
     * Return a unique ID that represents the request
     *
     * @return string
     */
    public function generate(): string;
}
