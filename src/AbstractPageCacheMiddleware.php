<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

use Laminas\Cache\Storage\Adapter\AbstractAdapter as StorageAdapter;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\AbstractIdGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;

abstract class AbstractPageCacheMiddleware implements MiddlewareInterface
{
    protected const STATUS_HIT  = 'Hit';

    protected const STATUS_MISS = 'Miss';

    private array          $config = [];

    private StorageAdapter $storageAdapter;

    public function getStorageAdapter(): StorageAdapter
    {
        return $this->storageAdapter;
    }

    public function setStorageAdapter(StorageAdapter $storageAdapter): self
    {
        $this->storageAdapter = $storageAdapter;

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    protected function getIdGenerator(): IdGeneratorInterface
    {
        $config = $this->getConfig();

        return new $config['id_generator'];
    }

    protected function shouldCache(ServerRequestInterface $request): bool
    {
        $config = $this->getConfig();

        if (!$config['enabled']) {
            return false;
        }

        $routeResult = $request->getAttribute(RouteResult::class);

        if (!$routeResult instanceof RouteResult) {
            return false;
        }

        $matchedRoute = $routeResult->getMatchedRoute();

        if (!$matchedRoute instanceof Route) {
            return false;
        }

        return in_array($matchedRoute->getName(), $config['handlers']);
    }
}
