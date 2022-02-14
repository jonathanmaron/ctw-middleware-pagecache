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

        $cacheId    = $idGenerator->generate($request);
        $serialized = $cache->getItem($cacheId, $success, $casToken);

        if (!$success) {
            $cacheStatus = self::STATUS_MISS;
            $response    = $handler->handle($request);
            $serialized  = ResponseSerializer::toArray($response);
            $cache->setItem($cacheId, $serialized);
        } else {
            $cacheStatus = self::STATUS_HIT;
            assert(is_array($serialized));
            $response    = ResponseSerializer::fromArray($serialized);
        }

        $response = $response->withHeader('X-Page-Cache', $cacheStatus);

        return $response;
    }
}
