<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver;

use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver as SymfonyBackedEnumValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BackedEnumValueResolverTest extends TestCase
{
    /**
     * @dataProvider provideTestSupportsData
     */
    public function testSupports(Request $request, ArgumentMetadata $metadata, bool $expectedSupport)
    {
        if (class_exists(SymfonyBackedEnumValueResolver::class)) {
            $this->markTestSkipped('This test is only relevant for Symfony <6.1. Use Symfony\'s resolver instead.');
        }

        $resolver = new BackedEnumValueResolver();

        self::assertSame($expectedSupport, $resolver->supports($request, $metadata));
    }

    public function provideTestSupportsData(): iterable
    {
        yield 'unsupported type' => [
            self::createRequest(['suit' => 'H']),
            self::createArgumentMetadata('suit', \stdClass::class),
            false,
        ];

        yield 'supports from attributes' => [
            self::createRequest(['suit' => 'H']),
            self::createArgumentMetadata('suit', Suit::class),
            true,
        ];

        yield 'with null attribute value' => [
            self::createRequest(['suit' => null]),
            self::createArgumentMetadata('suit', Suit::class),
            true,
        ];

        yield 'without matching attribute' => [
            self::createRequest(),
            self::createArgumentMetadata('suit', Suit::class),
            false,
        ];

        yield 'unsupported variadic' => [
            self::createRequest(['suit' => ['H', 'S']]),
            self::createArgumentMetadata(
                'suit',
                Suit::class,
                variadic: true,
            ),
            false,
        ];
    }

    /**
     * @dataProvider provideTestResolveData
     */
    public function testResolve(Request $request, ArgumentMetadata $metadata, $expected)
    {
        if (class_exists(SymfonyBackedEnumValueResolver::class)) {
            $this->markTestSkipped('This test is only relevant for Symfony <6.1. Use Symfony\'s resolver instead.');
        }

        $resolver = new BackedEnumValueResolver();
        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);

        self::assertSame($expected, \is_array($results) ? $results : iterator_to_array($results));
    }

    public function provideTestResolveData(): iterable
    {
        yield 'resolves from attributes' => [
            self::createRequest(['suit' => 'H']),
            self::createArgumentMetadata('suit', Suit::class),
            [Suit::Hearts],
        ];

        yield 'with null attribute value' => [
            self::createRequest(['suit' => null]),
            self::createArgumentMetadata(
                'suit',
                Suit::class,
            ),
            [null],
        ];
    }

    public function testResolveThrowsNotFoundOnInvalidValue()
    {
        if (class_exists(SymfonyBackedEnumValueResolver::class)) {
            $this->markTestSkipped('This test is only relevant for Symfony <6.1. Use Symfony\'s resolver instead.');
        }

        $resolver = new BackedEnumValueResolver();
        $request = self::createRequest(['suit' => 'foo']);
        $metadata = self::createArgumentMetadata('suit', Suit::class);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Could not resolve the "Elao\Enum\Tests\Fixtures\Enum\Suit $suit" controller argument: "foo" is not a valid backing value for enum');

        /** @var \Generator|array $results */
        $results = $resolver->resolve($request, $metadata);
        if (!\is_array($results)) {
            iterator_to_array($results);
        }
    }

    public function testResolveThrowsOnUnexpectedType()
    {
        if (class_exists(SymfonyBackedEnumValueResolver::class)) {
            $this->markTestSkipped('This test is only relevant for Symfony <6.1. Use Symfony\'s resolver instead.');
        }

        $resolver = new BackedEnumValueResolver();
        $request = self::createRequest(['suit' => false]);
        $metadata = self::createArgumentMetadata('suit', Suit::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Could not resolve the "Elao\Enum\Tests\Fixtures\Enum\Suit $suit" controller argument: expecting an int or string, got "bool".');

        /** @var \Generator|array $results */
        $results = $resolver->resolve($request, $metadata);
        if (!\is_array($results)) {
            iterator_to_array($results);
        }
    }

    private static function createRequest(array $attributes = []): Request
    {
        return new Request([], [], $attributes);
    }

    private static function createArgumentMetadata(string $name, string $type, bool $variadic = false): ArgumentMetadata
    {
        return new ArgumentMetadata($name, $type, $variadic, false, null);
    }
}
