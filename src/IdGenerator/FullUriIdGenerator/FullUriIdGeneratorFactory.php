<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Psr\Container\ContainerInterface;

class FullUriIdGeneratorFactory
{
    public function __invoke(ContainerInterface $container): FullUriIdGenerator
    {
        return new FullUriIdGenerator();
    }
}
