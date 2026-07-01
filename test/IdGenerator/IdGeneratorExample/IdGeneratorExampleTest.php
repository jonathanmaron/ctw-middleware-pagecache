<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorExample;

use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorExample\IdGeneratorExample;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\IdGeneratorExample\IdGeneratorExampleFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;

final class IdGeneratorExampleTest extends AbstractCase
{
    /**
     * Test that generate returns a 64-character sha256 hash for a plain request.
     */
    public function testGenerateReturnsSha256HashForRequestWithoutSession(): void
    {
        $request     = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $idGenerator = new IdGeneratorExample();

        $id = $idGenerator->generate($request);

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $id);
    }

    /**
     * Test that generate ignores the session value when no session is attached.
     */
    public function testGenerateIgnoresSessionWhenAttributeAbsent(): void
    {
        $request     = new ServerRequest([], [], new Uri('https://www.example.com/test/'));
        $idGenerator = new IdGeneratorExample();

        $withoutAttribute = $idGenerator->generate($request);
        $withAttribute    = $idGenerator->generate($request->withAttribute('attribute_key', 'value'));

        self::assertNotSame($withoutAttribute, $withAttribute);
    }

    /**
     * Test that generate skips the session value when the session lacks the key.
     */
    public function testGenerateSkipsSessionValueWhenKeyMissing(): void
    {
        $session = self::createStub(SessionInterface::class);
        $session->method('has')
            ->willReturn(false);

        $request = new ServerRequest([], [], new Uri('https://www.example.com/test/'))
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $idGenerator = new IdGeneratorExample();

        $withSession    = $idGenerator->generate($request);
        $withoutSession = $idGenerator->generate(
            new ServerRequest([], [], new Uri('https://www.example.com/test/')),
        );

        self::assertSame($withoutSession, $withSession);
    }

    /**
     * Test that generate incorporates the session value when the key is present.
     */
    public function testGenerateIncorporatesSessionValueWhenKeyPresent(): void
    {
        $session = self::createStub(SessionInterface::class);
        $session->method('has')
            ->willReturn(true);
        $session->method('get')
            ->willReturn('session-value');

        $request = new ServerRequest([], [], new Uri('https://www.example.com/test/'))
            ->withAttribute(SessionMiddleware::SESSION_ATTRIBUTE, $session);

        $idGenerator = new IdGeneratorExample();

        $withSessionValue = $idGenerator->generate($request);
        $withoutSession   = $idGenerator->generate(
            new ServerRequest([], [], new Uri('https://www.example.com/test/')),
        );

        self::assertNotSame($withoutSession, $withSessionValue);
    }

    /**
     * Test that generate ignores an attribute value that is neither scalar nor stringable.
     */
    public function testGenerateIgnoresNonScalarAttributeValue(): void
    {
        $idGenerator = new IdGeneratorExample();

        $withArrayAttribute = $idGenerator->generate(
            new ServerRequest([], [], new Uri('https://www.example.com/test/'))
                ->withAttribute('attribute_key', ['not', 'scalar']),
        );
        $withoutAttribute = $idGenerator->generate(
            new ServerRequest([], [], new Uri('https://www.example.com/test/')),
        );

        self::assertSame($withoutAttribute, $withArrayAttribute);
    }

    /**
     * Test that the factory builds a generator that hashes a request.
     */
    public function testFactoryBuildsGeneratorThatHashesRequest(): void
    {
        $container   = new ServiceManager();
        $factory     = new IdGeneratorExampleFactory();
        $idGenerator = $factory->__invoke($container);

        $request = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $idGenerator->generate($request));
    }
}
