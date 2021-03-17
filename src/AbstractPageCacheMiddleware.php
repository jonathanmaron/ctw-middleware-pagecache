<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;
use Laminas\Cache\Storage\Adapter\AbstractAdapter as StorageAdapter;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class AbstractPageCacheMiddleware implements MiddlewareInterface
{
    protected const STATUS_HIT  = 'Hit';

    protected const STATUS_MISS = 'Miss';

    private StorageAdapter       $storageAdapter;

    private IdGeneratorInterface $idGenerator;

    private bool                 $enabled;

    private array                $handlers;

    public function getStorageAdapter(): StorageAdapter
    {
        return $this->storageAdapter;
    }

    public function setStorageAdapter(StorageAdapter $storageAdapter): self
    {
        $this->storageAdapter = $storageAdapter;

        return $this;
    }

    public function getIdGenerator(): IdGeneratorInterface
    {
        return $this->idGenerator;
    }

    public function setIdGenerator(IdGeneratorInterface $idGenerator): self
    {
        $this->idGenerator = $idGenerator;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    public function setHandlers(array $handlers): AbstractPageCacheMiddleware
    {
        $this->handlers = $handlers;

        return $this;
    }

    protected function shouldCache(ServerRequestInterface $request): bool
    {
        if (!$this->isEnabled()) {
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

        return in_array($matchedRoute->getName(), $this->getHandlers());
    }
}
