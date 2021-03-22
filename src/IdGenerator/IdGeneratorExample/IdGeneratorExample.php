<?php
declare(strict_types=1);

namespace Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorExample;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\AbstractIdGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class IdGeneratorExample extends AbstractIdGenerator implements IdGeneratorInterface
{
    public function generate(ServerRequestInterface $request): string
    {
        /*
        // Available methods
        $request->getAttribute();
        $request->getAttributes();
        $request->getBody();
        $request->getCookieParams();
        $request->getHeader();
        $request->getHeaderLine();
        $request->getHeaders();
        $request->getMethod();
        $request->getParsedBody();
        $request->getProtocolVersion();
        $request->getQueryParams();
        $request->getRequestTarget();
        $request->getServerParams();
        $request->getUploadedFiles();
        $request->getUri();
        */

        $vars = [];

        // Access URI
        $vars[] = $request->getUri();

        // Access Attributes
        $vars[] = $request->getAttribute('attribute_key');

        // Access Session
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if ($session instanceof SessionInterface && $session->has('session_key')) {
            $vars[] = $session->get('session_key');
        }

        return $this->getHash($vars);
    }
}
