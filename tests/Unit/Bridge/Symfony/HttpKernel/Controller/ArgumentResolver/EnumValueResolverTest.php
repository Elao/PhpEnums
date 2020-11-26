<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver;

use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EnumValueResolverTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!interface_exists(ArgumentValueResolverInterface::class)) {
            self::markTestSkipped(
                sprintf('"%s" is not present in this Symfony version', ArgumentValueResolverInterface::class)
            );
        }
    }

    /**
     * @dataProvider inputDataProvider
     */
    public function testValidInput(ArgumentMetadata $metadata, Request $request, array $expected): void
    {
        $resolver = new EnumValueResolver();

        self::assertTrue($resolver->supports($request, $metadata));
        self::assertSame($expected, iterator_to_array($resolver->resolve($request, $metadata)));
    }

    public function inputDataProvider(): iterable
    {
        yield 'valid enum submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, false, false, null),
            Request::create('/', Request::METHOD_GET, ['permissions' => Permissions::READ]),
            [Permissions::READ()],
        ];
        yield 'optional value with nothing submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, false, true, null),
            Request::create('/'),
            [null],
        ];
        yield 'optional value with null submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, false, true, null),
            Request::create('/', Request::METHOD_GET, ['permissions' => null]),
            [null],
        ];
        yield 'nullable value with nothing submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, false, false, null, true),
            Request::create('/'),
            [null],
        ];
        yield 'nullable value with null submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, false, false, null, true),
            Request::create('/', Request::METHOD_GET, ['permissions' => null]),
            [null],
        ];
        yield 'nullable variadic with nothing submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, true, false, null, true),
            Request::create('/'),
            [null],
        ];
        yield 'nullable variadic with null submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, true, false, null, true),
            Request::create('/', Request::METHOD_GET, ['permissions' => null]),
            [null],
        ];
        yield 'empty variadic submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, true, false, null, true),
            Request::create('/', Request::METHOD_GET, ['permissions' => []]),
            [],
        ];
        yield 'valid variadic submitted' => [
            new ArgumentMetadata('permissions', Permissions::class, true, false, null, true),
            Request::create('/', Request::METHOD_GET, ['permissions' => [Permissions::READ, Permissions::WRITE]]),
            [Permissions::READ(), Permissions::WRITE()],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testinvalidInput($invalidInput, $expectedErrorMessage): void
    {
        $this->expectException(BadRequestHttpException::class);

        $resolver = new EnumValueResolver();
        $metadata = new ArgumentMetadata('permissions', Permissions::class, false, false, null);

        $request = Request::create('/', Request::METHOD_GET, ['permissions' => $invalidInput]);
        self::assertTrue($resolver->supports($request, $metadata));

        $this->expectExceptionMessage($expectedErrorMessage);

        iterator_to_array($resolver->resolve($request, $metadata));
    }

    public function invalidDataProvider(): iterable
    {
        yield [null, 'Enum "Elao\Enum\Tests\Fixtures\Enum\Permissions" does not accept value NULL'];
        yield [[], 'Enum "Elao\Enum\Tests\Fixtures\Enum\Permissions" does not accept value array (' . "\n" . ')'];
    }
}
