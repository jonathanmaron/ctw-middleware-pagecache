<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Psr\Container\ContainerInterface;

class RequestUriGeneratorFactory
{
    public function __invoke(ContainerInterface $container): RequestUriGenerator
    {
        return new RequestUriGenerator();
    }
}
