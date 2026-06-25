<?php

declare(strict_types=1);

namespace CtwTest\Middleware\PageCacheMiddleware\Exception;

use Ctw\Middleware\PageCacheMiddleware\Exception\ExceptionInterface;
use Ctw\Middleware\PageCacheMiddleware\Exception\InvalidArgumentException;
use Ctw\Middleware\PageCacheMiddleware\Exception\RuntimeException;
use Ctw\Middleware\PageCacheMiddleware\Exception\UnexpectedValueException;
use CtwTest\Middleware\PageCacheMiddleware\AbstractCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ExceptionTest extends AbstractCase
{
    /**
     * Test that each package exception implements the shared exception interface.
     *
     * @param class-string $exceptionClass
     */
    #[DataProvider('exceptionClassProvider')]
    public function testPackageExceptionImplementsExceptionInterface(string $exceptionClass): void
    {
        self::assertContains(ExceptionInterface::class, class_implements($exceptionClass));
    }

    /**
     * Test that each package exception preserves the message passed to its constructor.
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    #[DataProvider('exceptionClassProvider')]
    public function testPackageExceptionPreservesConstructorMessage(string $exceptionClass): void
    {
        $exception = new $exceptionClass('the-message');

        self::assertSame('the-message', $exception->getMessage());
    }

    /**
     * Test that each package exception extends its matching SPL parent class.
     *
     * @param class-string $exceptionClass
     * @param class-string $splParentClass
     */
    #[DataProvider('exceptionParentProvider')]
    public function testPackageExceptionExtendsSplParent(string $exceptionClass, string $splParentClass): void
    {
        self::assertContains($splParentClass, class_parents($exceptionClass));
    }

    /**
     * Test that a package exception is catchable through the shared interface.
     */
    public function testPackageExceptionIsCatchableViaSharedInterface(): void
    {
        $caught = null;

        try {
            throw new RuntimeException('caught via interface');
        } catch (ExceptionInterface $exception) {
            $caught = $exception->getMessage();
        }

        self::assertSame('caught via interface', $caught);
    }

    /**
     * @return array<string, array{class-string<\Throwable>}>
     */
    public static function exceptionClassProvider(): array
    {
        return [
            'runtime'          => [RuntimeException::class],
            'invalid argument' => [InvalidArgumentException::class],
            'unexpected value' => [UnexpectedValueException::class],
        ];
    }

    /**
     * @return array<string, array{class-string, class-string}>
     */
    public static function exceptionParentProvider(): array
    {
        return [
            'runtime'          => [RuntimeException::class, \RuntimeException::class],
            'invalid argument' => [InvalidArgumentException::class, \InvalidArgumentException::class],
            'unexpected value' => [UnexpectedValueException::class, \UnexpectedValueException::class],
        ];
    }
}
