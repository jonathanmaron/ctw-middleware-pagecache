<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;
use Ctw\Middleware\PageCacheMiddleware\Strategy\StrategyInterface;
use Laminas\Cache\Storage\Adapter\AbstractAdapter as StorageAdapter;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class AbstractPageCacheMiddleware implements MiddlewareInterface
{
    protected const STATUS_HIT  = 'Hit';

    protected const STATUS_MISS = 'Miss';

    private StorageAdapter       $storageAdapter;

    private IdGeneratorInterface $idGenerator;

    private StrategyInterface    $strategy;

    private bool                 $enabled;

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

    public function getStrategy(): StrategyInterface
    {
        return $this->strategy;
    }

    public function setStrategy(StrategyInterface $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    protected function shouldCache(ServerRequestInterface $request): bool
    {
        if (!$this->getEnabled()) {
            return false;
        }

        return $this->getStrategy()->shouldCache($request);
    }
}
