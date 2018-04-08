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

        $gender = $enumProvider->enum('Elao\Enum\Tests\Fixtures\Enum\Gender::MALE');
        $this->assertInstanceOf(Gender::class, $gender);

        $permissions = $enumProvider->randomEnum('Permissions');
        $this->assertInstanceOf(Permissions::class, $permissions);
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidArgumentException
     *
     * @dataProvider provideErroneousEnumMapping
     */
    public function testConstructorShouldFailWhenEnumClassIsIncorrect(array $mapping)
    {
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

    /**
     * @expectedException \Elao\Enum\Exception\InvalidArgumentException
     */
    public function testEnumMethodShouldThrowErrorIfFlaggedEnumIsIncorrect()
    {
        $enumProvider = $this->getDefaultProvider();

        // Simple is not a flagged enum. An InvalidArgumentException should therefore be thrown.
        $enumProvider->enum('Simple::FIRST|SECOND');
    }

    private function getDefaultProvider(): EnumProvider
    {
        return new EnumProvider([
            'Simple' => SimpleEnum::class,
            'Permissions' => Permissions::class,
        ]);
    }
}
