<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class FullUriIdGenerator extends AbstractIdGenerator implements IdGeneratorInterface
{
    /**
     * Generate an ID based on the request's host, port, path and query
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

        $port  = $uri->getPort();
        $host  = $uri->getHost();
        $query = $uri->getQuery();

        $data = (string) self::SALT . $host . $port . $path . $query;

        return hash('sha256', $data);
    }
}
