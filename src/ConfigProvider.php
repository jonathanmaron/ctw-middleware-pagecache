<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                PageCacheMiddleware::class => PageCacheMiddlewareFactory::class,
            ],
        ];
    }
}
