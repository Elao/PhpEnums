<?php

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
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use PHPUnit\Framework\TestCase;

class EnumProviderTest extends TestCase
{
    public function testEnumMethodShouldReturnExpectedEnums()
    {
        $enumProvider = $this->getDefaultProvider();

        $simple = $enumProvider->enum('Simple::FIRST');
        $this->assertInstanceOf(SimpleEnum::class, $simple);
        $this->assertTrue($simple->is(SimpleEnum::FIRST));

        $gender = $enumProvider->enum('Elao\Enum\Tests\Fixtures\Enum\Gender::MALE');
        $this->assertInstanceOf(Gender::class, $gender);
        $this->assertTrue($gender->is(Gender::MALE));

        /* @var Permissions $permissions */
        $permissions = $enumProvider->enum('Permissions::READ|WRITE');
        $this->assertInstanceOf(Permissions::class, $permissions);
        $this->assertTrue($permissions->hasFlag(Permissions::READ));
        $this->assertTrue($permissions->hasFlag(Permissions::WRITE));
        $this->assertFalse($permissions->hasFlag(Permissions::EXECUTE));
    }

    public function testRandomEnumMethodShouldReturnExpectedEnums()
    {
        $enumProvider = $this->getDefaultProvider();

        $simple = $enumProvider->randomEnum('Simple');
        $this->assertInstanceOf(SimpleEnum::class, $simple);

        $gender = $enumProvider->randomEnum(Gender::class);
        $this->assertInstanceOf(Gender::class, $gender);

        $permissions = $enumProvider->randomEnum('Permissions');
        $this->assertInstanceOf(Permissions::class, $permissions);
    }

    /**
     * @dataProvider provideErroneousEnumMapping
     */
    public function testConstructorShouldFailWhenEnumClassIsIncorrect(array $mapping)
    {
        $this->expectException(InvalidArgumentException::class);

        new EnumProvider($mapping);
    }

    public function provideErroneousEnumMapping()
    {
        yield 'EnumProvider constructor with a class that does not exist' => [
            [
                'Simple' => SimpleEnum::class,
                'Not-a-class' => '\UnexistingClass',
            ],
        ];

        yield 'EnumProvider constructor with a class that is not an Enum' => [
            [
                'Simple' => SimpleEnum::class,
                'Not-an-enum' => \DateTime::class,
            ],
        ];
    }

    public function testEnumMethodShouldThrowErrorIfFlaggedEnumIsIncorrect()
    {
        $this->expectException(InvalidArgumentException::class);

        $enumProvider = $this->getDefaultProvider();

        // Simple is not a flagged enum. An InvalidArgumentException should therefore be thrown.
        $enumProvider->enum('Simple::FIRST|SECOND');
    }

    public function testRandomEnums()
    {
        $enumProvider = $this->getDefaultProvider();
        $nbOfSimpleValues = \count(SimpleEnum::values());

        // Test generating all values in random order (no duplicates)
        $simples = $enumProvider->randomEnums('Simple', $nbOfSimpleValues, false);
        self::assertEmpty(array_udiff(
            $simples,
            SimpleEnum::instances(),
            static function ($a, $b) { return $a === $b; }
        ), 'contains all unique enumeration values');

        $count = \count($enumProvider->randomEnums('Simple', $max = 1));
        self::assertTrue($count >= 0, 'at least 1 variable nb of enum generated');

        $count = \count($enumProvider->randomEnums('Simple', $max = 3, true, $min = 1));
        self::assertTrue($count >= $min && $count <= $max, "variable nb of enum generated beween $min and $max");

        $count = \count($enumProvider->randomEnums('Simple', $nbOfSimpleValues + 1, false));
        self::assertSame($count, $nbOfSimpleValues, 'won\'t exceed maximum nb of values for the enum');
    }

    public function testEnumMethodShouldThrowAnInvalidArgumentException()
    {
        $enumProvider = $this->getDefaultProvider();

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('"SomeRandomStringToFail" is not a proper enum class');
        $enumProvider->enum('SomeRandomStringToFail::FIRST');
    }

    private function getDefaultProvider(): EnumProvider
    {
        return new EnumProvider([
            'Simple' => SimpleEnum::class,
            'Permissions' => Permissions::class,
        ]);
    }
}
