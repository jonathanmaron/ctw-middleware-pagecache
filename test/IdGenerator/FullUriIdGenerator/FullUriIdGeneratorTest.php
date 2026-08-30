<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator;

use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGenerator;
use Ctw\Middleware\PageCacheMiddleware\IdGenerator\FullUriIdGenerator\FullUriIdGeneratorFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\ServiceManager\ServiceManager;

final class FullUriIdGeneratorTest extends AbstractCase
{
    /**
     * Test that the factory builds a generator that hashes the full request URI.
     */
    public function testFactoryBuildsGeneratorThatHashesFullUri(): void
    {
        $container   = new ServiceManager();
        $factory     = new FullUriIdGeneratorFactory();
        $idGenerator = $factory->__invoke($container);

        $request  = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $expected = 'c386fa280ba039f4160541d2c1d78af327cacee1519bfcf0177ed8ba7560cf46';

        self::assertSame($expected, $idGenerator->generate($request));
    }

    /**
     * Test that generate returns the expected sha256 hash for a full URI.
     */
    public function testGenerateReturnsExpectedHashForFullUri(): void
    {
        $request     = new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1'));
        $idGenerator = new FullUriIdGenerator();

        $expected = 'c386fa280ba039f4160541d2c1d78af327cacee1519bfcf0177ed8ba7560cf46';

        self::assertSame($expected, $idGenerator->generate($request));
    }

    /**
     * Test that generate produces different hashes when the query string differs.
     */
    public function testGenerateProducesDifferentHashWhenQueryDiffers(): void
    {
        $idGenerator = new FullUriIdGenerator();

        $first  = $idGenerator->generate(new ServerRequest([], [], new Uri('https://www.example.com/test/?a=1')));
        $second = $idGenerator->generate(new ServerRequest([], [], new Uri('https://www.example.com/test/?a=2')));

        self::assertNotSame($first, $second);
    }

    /**
     * Test that generate ignores empty host, port and query segments when hashing.
     */
    public function testGenerateIgnoresEmptyUriSegments(): void
    {
        $idGenerator = new FullUriIdGenerator();

        $pathOnly = $idGenerator->generate(new ServerRequest([], [], new Uri('/test/')));
        $expected = 'acd068c803f8551daf9d14db5f3cd6131cdfbe53138c93085575af148e826ae6';

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $pathOnly);
        self::assertSame($expected, $pathOnly);
    }

    /**
     * Test that generate throws when the request path cannot be auto-detected.
     */
    public function testGenerateThrowsRuntimeExceptionWhenPathIsEmpty(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains('Cannot auto-detect current page identity');

        $request     = new ServerRequest([], [], new Uri());
        $idGenerator = new FullUriIdGenerator();

        $idGenerator->generate($request);
    }
}
