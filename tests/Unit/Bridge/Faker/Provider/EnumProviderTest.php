<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Faker\Provider;

use DateTime;
use Elao\Enum\Bridge\Faker\Provider\EnumProvider;
use Elao\Enum\Exception\InvalidArgumentException;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;

class EnumProviderTest extends TestCase
{
    public function testRandomEnumMethodShouldReturnExpectedEnums(): void
    {
        $enumProvider = $this->getDefaultProvider();

        $simple = $enumProvider->randomEnum('Suit');
        self::assertInstanceOf(Suit::class, $simple);

        $gender = $enumProvider->randomEnum(Suit::class);
        self::assertInstanceOf(Suit::class, $gender);

        $permissions = $enumProvider->randomEnum('Permissions');
        self::assertInstanceOf(Permissions::class, $permissions);
    }

    /**
     * @dataProvider provideErroneousEnumMapping
     */
    public function testConstructorShouldFailWhenEnumClassIsIncorrect(array $mapping): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EnumProvider($mapping);
    }

    public function provideErroneousEnumMapping(): iterable
    {
        yield 'EnumProvider constructor with a class that does not exist' => [[
            'Suit' => Suit::class,
            'Not-a-class' => '\UnexistingClass',
        ]];

        yield 'EnumProvider constructor with a class that is not an Enum' => [[
            'Suit' => Suit::class,
            'Not-an-enum' => DateTime::class,
        ]];
    }

    private function getDefaultProvider(): EnumProvider
    {
        return new EnumProvider([
            'Suit' => Suit::class,
            'Permissions' => Permissions::class,
        ]);
    }
}
