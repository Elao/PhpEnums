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

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromBody;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromQuery;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\QueryBodyBackedEnumValueResolver;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class QueryBodyBackedEnumValueResolverTest extends TestCase
{
    /**
     * @dataProvider provides testSupports data
     */
    public function testSupports(Request $request, ArgumentMetadata $metadata, bool $expectedSupport): void
    {
        $resolver = new QueryBodyBackedEnumValueResolver();

        // Before Symfony 6.2
        if (!interface_exists(ValueResolverInterface::class)) {
            self::assertSame($expectedSupport, $resolver->supports($request, $metadata));

            return;
        }

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        $results = iterator_to_array($results);

        $expectedSupport ? self::assertNotEmpty($results) : self::assertSame([], $results);
    }

    public function provides testSupports data(): iterable
    {
        yield 'no PHP 8.1 attribute' => [
            self::getRequest(['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                \stdClass::class,
                attributes: []
            ),
            false,
        ];

        yield 'unsupported type' => [
            self::getRequest(['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                \stdClass::class,
                attributes: [new BackedEnumFromQuery()]
            ),
            false,
        ];

        yield 'from query' => [
            self::getRequest(query: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromQuery()],
            ),
            true,
        ];

        yield 'missing from query' => [
            self::getRequest(query: []),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromQuery()],
            ),
            false,
        ];

        yield 'from body' => [
            self::getRequest(body: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromBody()],
            ),
            true,
        ];

        yield 'missing from body' => [
            self::getRequest(body: []),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromBody()],
            ),
            false,
        ];

        yield 'non-nullable with null found (casted from empty string)' => [
            self::getRequest(query: ['suit' => '']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromQuery()],
            ),
            false,
        ];

        yield 'nullable with null found (casted from empty string)' => [
            self::getRequest(query: ['suit' => '']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: true,
                attributes: [new BackedEnumFromQuery()],
            ),
            true,
        ];

        yield 'non-nullable with no value found' => [
            self::getRequest(),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromQuery()],
            ),
            false,
        ];

        yield 'supports variadics' => [
            self::getRequest(query: ['suits' => ['H', 'S']]),
            self::getArgumentMetadata(
                'suits',
                Suit::class,
                variadic: true,
                attributes: [new BackedEnumFromQuery()],
            ),
            true,
        ];
    }

    /**
     * @dataProvider provides testResolve data
     */
    public function testResolve(Request $request, ArgumentMetadata $metadata, $expected): void
    {
        $resolver = new QueryBodyBackedEnumValueResolver();

        // Before Symfony 6.2
        if (!interface_exists(ValueResolverInterface::class)) {
            if (!$resolver->supports($request, $metadata)) {
                throw new \LogicException(sprintf(
                    'Invalid test case %s, since the supports method returned false',
                    $this->getName(true),
                ));
            }

            return;
        }

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        $results = iterator_to_array($results);

        if ([] === $results) {
            throw new \LogicException(sprintf(
                'Invalid test case %s, since the supports method returned false',
                $this->getName(true),
            ));
        }

        self::assertSame($expected, $results);
    }

    public function provides testResolve data(): iterable
    {
        yield 'from query' => [
            self::getRequest(query: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromQuery()],
            ),
            [Suit::Hearts],
        ];

        yield 'from body' => [
            self::getRequest(body: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: [new BackedEnumFromBody()],
            ),
            [Suit::Hearts],
        ];

        yield 'nullable with null found (casted from empty string)' => [
            self::getRequest(query: ['suit' => '']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: true,
                attributes: [new BackedEnumFromQuery()],
            ),
            [null],
        ];

        yield 'with variadics' => [
            self::getRequest(query: ['suit' => ['H', 'S']]),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                variadic: true,
                attributes: [new BackedEnumFromQuery()],
            ),
            [Suit::Hearts, Suit::Spades],
        ];

        yield 'nullable, with variadics' => [
            self::getRequest(query: ['suit' => ['', '']]),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: true,
                variadic: true,
                attributes: [new BackedEnumFromQuery()],
            ),
            [null, null],
        ];
    }

    public function testResolveThrowsOnInvalidValue(): void
    {
        $resolver = new QueryBodyBackedEnumValueResolver();
        $request = self::getRequest(query: ['suit' => 'foo']);
        $metadata = self::getArgumentMetadata('suit', Suit::class, attributes: [new BackedEnumFromQuery()]);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Could not resolve the "Elao\Enum\Tests\Fixtures\Enum\Suit $suit" controller argument: "foo" is not a valid backing value for enum');

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        iterator_to_array($results);
    }

    public function testResolveThrowsUnexpectedType(): void
    {
        $resolver = new QueryBodyBackedEnumValueResolver();
        $request = self::getRequest(query: ['suit' => true]);
        $metadata = self::getArgumentMetadata('suit', Suit::class, attributes: [new BackedEnumFromQuery()]);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Could not resolve the "Elao\Enum\Tests\Fixtures\Enum\Suit $suit" controller argument: Elao\Enum\Tests\Fixtures\Enum\Suit::from(): Argument #1 ($value) must be of type string, bool given');

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        iterator_to_array($results);
    }

    public function testResolveThrowsNonVariadicsArrayValue(): void
    {
        if (!InstalledVersions::satisfies(new VersionParser(), 'symfony/http-kernel', '^6.0')) {
            self::markTestSkipped();
        }

        $resolver = new QueryBodyBackedEnumValueResolver();
        $request = self::getRequest(query: ['suit' => ['H', 'S']]);
        $metadata = self::getArgumentMetadata('suit', Suit::class, attributes: [new BackedEnumFromQuery()]);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Input value "suit" contains a non-scalar value.');

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        iterator_to_array($results);
    }

    public function testResolveThrowsVariadicsScalarValue(): void
    {
        $resolver = new QueryBodyBackedEnumValueResolver();
        $request = self::getRequest(query: ['suit' => 'H']);
        $metadata = self::getArgumentMetadata('suit', Suit::class, variadic: true, attributes: [new BackedEnumFromQuery()]);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Unexpected value for parameter "suit": expecting "array", got "string".');

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        iterator_to_array($results);
    }

    private static function getRequest(array $query = [], array $body = []): Request
    {
        $request = new Request();

        $request->query->replace($query);
        $request->request->replace($body);

        return $request;
    }

    private static function getArgumentMetadata(
        string $name,
        string $type,
        bool $nullable = false,
        bool $variadic = false,
        array $attributes = []
    ): ArgumentMetadata {
        return new ArgumentMetadata($name, $type, $variadic, false, null, $nullable, $attributes);
    }
}
