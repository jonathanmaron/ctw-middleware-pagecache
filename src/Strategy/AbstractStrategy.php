<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\Strategy;

abstract class AbstractStrategy
{
    private array $config;

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
