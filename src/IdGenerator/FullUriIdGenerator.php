<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;

class FullUriIdGenerator extends AbstractIdGenerator implements IdGeneratorInterface
{
    /**
     * Emergency invalidation salt
     *
     * @var string
     */
    private const SALT = 'viShjAuUnsWzxRXTrJiwpyg1CFZIAb4RqiBedLcq';

    /**
     * Generate an ID based on the request URI, HTTP host and server port.
     *
     * @return string
     */
    public function generate(): string
    {
        $requestUri = $this->getServerParam('REQUEST_URI');

        if (empty($requestUri)) {
            $message = "Cannot auto-detect current page identity";
            throw new RuntimeException($message);
        }

        $httpHost   = $this->getServerParam('HTTP_HOST');
        $serverPort = $this->getServerParam('SERVER_PORT');

        return $this->getHash($httpHost, $serverPort, $requestUri);
    }

    /**
     * Retrieve and normalize server variable.
     *
     * @param string $key
     *
     * @return string
     */
    private function getServerParam(string $key): string
    {
        if (!isset($_SERVER[$key])) {
            return '';
        }

        $value = (string) $_SERVER[$key];
        $value = trim($value);
        $value = strtolower($value);

        return $value;
    }

    /**
     * Using the params, create a SHA256 hash.
     *
     * @param string $httpHost
     * @param string $serverPort
     * @param string $requestUri
     *
     * @return string
     */
    private function getHash(string $httpHost, string $serverPort, string $requestUri): string
    {
        $data = sprintf('%s|%s|%s|%s', self::SALT, $httpHost, $serverPort, $requestUri);

        return hash('sha256', $data);
    }
}
