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

        if (0 === strlen($uri->getPath())) {
            $message = 'Cannot auto-detect current page identity';
            throw new RuntimeException($message);
        }

        $vars = [
            self::SALT,
            $uri->getQuery(),
        ];

        return $this->getHash($vars);
    }
}
