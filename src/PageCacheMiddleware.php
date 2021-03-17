<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware;

use Laminas\Diactoros\Response\ArraySerializer as ResponseSerializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PageCacheMiddleware extends AbstractPageCacheMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->shouldCache($request)) {
            return $handler->handle($request);
        }

        $idGenerator = $this->getIdGenerator();
        $cache       = $this->getStorageAdapter();

        $cacheId    = $idGenerator->generate();
        $serialized = $cache->getItem($cacheId, $success, $casToken);

        if (!$success) {
            $cacheStatus = self::STATUS_MISS;
            $response    = $handler->handle($request);
            $ttl         = (int) $cache->getOptions()->getTtl();
            $timestamp   = $ttl + time();
            $expires     = gmdate('D, d M Y H:i:s', $timestamp) . ' GMT';
            $response    = $response->withHeader('Expires', $expires);
            $serialized  = ResponseSerializer::toArray($response);
            $cache->setItem($cacheId, $serialized);
        } else {
            $cacheStatus = self::STATUS_HIT;
            $response    = ResponseSerializer::fromArray($serialized);
        }

        $response = $response->withHeader('X-Page-Cache', $cacheStatus);

        return $response;
    }
}
