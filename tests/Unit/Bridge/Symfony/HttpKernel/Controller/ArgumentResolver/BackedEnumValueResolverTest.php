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
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver\ResolveBackedEnumValue;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver\ResolveFrom;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class BackedEnumValueResolverTest extends TestCase
{
    private static function getResolveFromAttributes(ResolveFrom ...$from): array
    {
        return [new ResolveBackedEnumValue($from)];
    }

    /**
     * @dataProvider provides testSupports data
     */
    public function testSupports(Request $request, ArgumentMetadata $metadata, bool $expectedSupport): void
    {
        $resolver = new BackedEnumValueResolver();

        self::assertSame($expectedSupport, $resolver->supports($request, $metadata));
    }

    public function provides testSupports data(): iterable
    {
        yield 'unsupported type' => [
            self::getRequest(['suit' => 'H']),
            self::getArgumentMetadata('suit', \stdClass::class),
            false,
        ];

        yield 'missing argument value for name' => [
            self::getRequest(attributes: ['suit' => 'H']),
            self::getArgumentMetadata('wrong_name', Suit::class),
            false,
        ];

        yield 'defaults from attributes' => [
            self::getRequest(attributes: ['suit' => 'H']),
            self::getArgumentMetadata('suit', Suit::class),
            true,
        ];

        yield 'explicit from attributes' => [
            self::getRequest(attributes: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Attributes),
            ),
            true,
        ];

        yield 'explicit from attributes (absent)' => [
            self::getRequest(attributes: []),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Attributes),
            ),
            false,
        ];

        yield 'explicit from query' => [
            self::getRequest(query: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            true,
        ];

        yield 'explicit from query (absent)' => [
            self::getRequest(query: []),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            false,
        ];

        yield 'explicit from body' => [
            self::getRequest(body: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Body),
            ),
            true,
        ];

        yield 'explicit from body (absent)' => [
            self::getRequest(body: []),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Body),
            ),
            false,
        ];

        yield 'non-nullable with null found (casted from empty string)' => [
            self::getRequest(query: ['suit' => '']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: false,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            false,
        ];

        yield 'nullable with null found (casted from empty string)' => [
            self::getRequest(query: ['suit' => '']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: true,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            true,
        ];

        yield 'non-nullable with no value found' => [
            self::getRequest(),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: false,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            false,
        ];

        yield 'supports variadics' => [
            self::getRequest(query: ['suit' => ['H', 'S']]),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                variadic: true,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            true,
        ];
    }

    /**
     * @dataProvider provides testResolve data
     */
    public function testResolve(Request $request, ArgumentMetadata $metadata, $expected): void
    {
        $resolver = new BackedEnumValueResolver();

        if (!$resolver->supports($request, $metadata)) {
            throw new \LogicException(sprintf(
                'Invalid test case %s, since the supports method returned false',
                $this->getName(true),
            ));
        }

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);

        self::assertSame($expected, iterator_to_array($results));
    }

    public function provides testResolve data(): iterable
    {
        yield 'defaults from attributes' => [
            self::getRequest(attributes: ['suit' => 'H']),
            self::getArgumentMetadata('suit', Suit::class),
            [Suit::Hearts],
        ];

        yield 'explicit from attributes' => [
            self::getRequest(attributes: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Attributes),
            ),
            [Suit::Hearts],
        ];

        yield 'explicit from query' => [
            self::getRequest(query: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            [Suit::Hearts],
        ];

        yield 'explicit from body' => [
            self::getRequest(body: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Body),
            ),
            [Suit::Hearts],
        ];

        yield 'explicit from query or body' => [
            self::getRequest(query: ['suit' => 'S'], body: ['suit' => 'H']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query, ResolveFrom::Body),
            ),
            [Suit::Spades],
        ];

        yield 'nullable with null found (casted from empty string)' => [
            self::getRequest(query: ['suit' => '']),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                nullable: true,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            [null],
        ];

        yield 'with variadics' => [
            self::getRequest(query: ['suit' => ['H', 'S']]),
            self::getArgumentMetadata(
                'suit',
                Suit::class,
                variadic: true,
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
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
                attributes: self::getResolveFromAttributes(ResolveFrom::Query),
            ),
            [null, null],
        ];
    }

    public function testResolveThrowsOnInvalidValue(): void
    {
        $resolver = new BackedEnumValueResolver();
        $request = self::getRequest(attributes: ['suit' => 'foo']);
        $metadata = self::getArgumentMetadata('suit', Suit::class);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Enum type "Elao\Enum\Tests\Fixtures\Enum\Suit" does not accept value "foo"');

        /** @var \Generator $results */
        $results = $resolver->resolve($request, $metadata);
        iterator_to_array($results);
    }

    private static function getRequest(array $attributes = [], array $query = [], array $body = []): Request
    {
        $request = new Request();

        $request->attributes->replace($attributes);
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
