<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;

class FullUriIdGenerator extends AbstractIdGenerator implements IdGeneratorInterface
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

        $httpHost   = $this->getServerParam('HTTP_HOST');
        $serverPort = $this->getServerParam('SERVER_PORT');

        return $this->getHash($httpHost, $serverPort, $requestUri);
    }
}