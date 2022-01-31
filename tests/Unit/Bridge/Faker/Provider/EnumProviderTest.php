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

    public function testRandomEnums(): void
    {
        $enumProvider = $this->getDefaultProvider();
        $nbCases = \count(Suit::cases());

        // Test generating all values in random order (no duplicates)
        $suits = $enumProvider->randomEnums(Suit::class, $nbCases, false);
        self::assertEmpty(array_udiff(
            $suits,
            Suit::cases(),
            static function ($a, $b) { return $a === $b ? 1 : 0; }
        ), 'contains all unique enum cases');

        $count = \count($enumProvider->randomEnums(Suit::class, $max = 1));
        self::assertTrue($count >= 0, "at least $max variable nb of enum generated");

        $count = \count($enumProvider->randomEnums(Suit::class, $max = 3, false, $min = 1));
        self::assertTrue($count >= $min && $count <= $max, "variable nb of enum cases selected between $min and $max");

        $count = \count($enumProvider->randomEnums(Suit::class, $max = 3, true));
        self::assertSame($max, $count, "exactly $max nb of enum cases selected");

        $count = \count($enumProvider->randomEnums(Suit::class, $nbCases + 1, true));
        self::assertSame($count, $nbCases, 'won\'t exceed maximum nb of cases for the enum');
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
            'Not-an-enum' => \stdClass::class,
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
