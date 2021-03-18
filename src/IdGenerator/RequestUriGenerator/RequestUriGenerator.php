<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestUriGenerator extends AbstractIdGenerator implements IdGeneratorInterface
{
    /**
     * Generate an ID based on the request's path and query
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function generate(ServerRequestInterface $request): string
    {
        $uri = $request->getUri();

        $path = $uri->getPath();

        if (0 === strlen($path)) {
            $message = "Cannot auto-detect current page identity";
            throw new RuntimeException($message);
        }

        $query = $uri->getQuery();

        $data = (string) self::SALT . $path . $query;

        return hash('sha256', $data);
    }
}
