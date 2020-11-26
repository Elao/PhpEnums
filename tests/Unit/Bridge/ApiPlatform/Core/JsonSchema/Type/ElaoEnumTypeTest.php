<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\ApiPlatform\Core\JsonSchema\Type;

use ApiPlatform\Core\JsonSchema\TypeFactory;
use Elao\Enum\Bridge\ApiPlatform\Core\JsonSchema\Type\ElaoEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\PropertyInfo\Type;

class ElaoEnumTypeTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testGetType(array $expected, Type $type): void
    {
        if (!interface_exists(\ApiPlatform\Core\Exception\ExceptionInterface::class)) {
            self::markTestSkipped('API platform not installed');
        }

        $typeFactory = new ElaoEnumType(new TypeFactory());
        self::assertEquals($expected, $typeFactory->getType($type, 'json'));
    }

    public function typeProvider(): iterable
    {
        yield [
            [
                'type' => 'string',
                'enum' => [
                    0 => 'unknown',
                    1 => 'male',
                    2 => 'female',
                ],
                'example' => 'unknown',
            ],
            new Type(Type::BUILTIN_TYPE_OBJECT, true, Gender::class),
        ];
        yield [['type' => 'integer'], new Type(Type::BUILTIN_TYPE_INT)];
    }
}
