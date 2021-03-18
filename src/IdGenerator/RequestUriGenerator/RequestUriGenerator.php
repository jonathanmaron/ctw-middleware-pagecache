<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;

class RequestUriGenerator extends AbstractIdGenerator implements IdGeneratorInterface
{
    /**
     * Generate an ID based on the request URI, HTTP host and server port.
     *
     * @return string
     */
    public function generate(): string
    {
        $requestUri = $this->getServerParam('REQUEST_URI');

        if (0 === strlen($requestUri)) {
            $message = "Cannot auto-detect current page identity";
            throw new RuntimeException($message);
        }

        return $this->getHash($requestUri);
    }
}
