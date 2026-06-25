<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\RequestUriGenerator\RequestUriGeneratorFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;

final class RequestUriGeneratorTest extends AbstractCase
{
    /**
     * Test that the factory builds a generator that hashes the request query.
     */
    public function testFactoryBuildsGeneratorThatHashesRequestQuery(): void
    {
        $container   = new ServiceManager();
        $factory     = new RequestUriGeneratorFactory();
        $idGenerator = $factory->__invoke($container);

        $request  = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $expected = 'abc876abc281af806683bef2805b826568b04e786ac354e1c7552571005f3b07';

        self::assertSame($expected, $idGenerator->generate($request));
    }

    /**
     * Test that generate returns the expected sha256 hash for a request URI with a query.
     */
    public function testGenerateReturnsExpectedHashForRequestWithQuery(): void
    {
        $request     = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $idGenerator = new RequestUriGenerator();

        $expected = 'abc876abc281af806683bef2805b826568b04e786ac354e1c7552571005f3b07';

        self::assertSame($expected, $idGenerator->generate($request));
    }

    /**
     * Test that generate ignores host, port and path so identical queries hash alike.
     */
    public function testGenerateDependsOnlyOnQueryString(): void
    {
        $idGenerator = new RequestUriGenerator();

        $first  = $idGenerator->generate(new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1')));
        $second = $idGenerator->generate(
            new ServerRequest([], [], new Uri('https://other.example.org/different/?a=1'))
        );

        self::assertSame($first, $second);
    }

    /**
     * Test that generate produces a hash from the salt alone when the query is empty.
     */
    public function testGenerateHashesSaltOnlyWhenQueryIsEmpty(): void
    {
        $idGenerator = new RequestUriGenerator();

        $id = $idGenerator->generate(new ServerRequest([], [], new Uri('https://www.example.com/test/')));

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $id);
    }

    /**
     * Test that generate throws when the request path cannot be auto-detected.
     */
    public function testGenerateThrowsRuntimeExceptionWhenPathIsEmpty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot auto-detect current page identity');

        $request     = new ServerRequest([], [], new Uri());
        $idGenerator = new RequestUriGenerator();

        $idGenerator->generate($request);
    }
}
