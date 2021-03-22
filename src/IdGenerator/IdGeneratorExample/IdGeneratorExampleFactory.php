<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorExample;

use Psr\Container\ContainerInterface;

class IdGeneratorExampleFactory
{
    public function __invoke(ContainerInterface $container): IdGeneratorExample
    {
        return new IdGeneratorExample();
    }
}
