<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator;

interface IdGeneratorInterface
{
    public function generate(): string;
}
