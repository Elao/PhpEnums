<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Faker\Provider;

use Elao\Enum\Bridge\Faker\Provider\EnumProvider;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class EnumProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testEnumMethodShouldReturnExpectedEnums()
    {
        $enumProvider = $this->getDefaultProvider();

        $simple = $enumProvider->enum('Simple::FIRST');
        $this->assertInstanceOf(SimpleEnum::class, $simple);
        $this->assertTrue($simple->is(SimpleEnum::FIRST));

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
        $enumProvider = new EnumProvider($mapping);
    }

    public function provideErroneousEnumMapping(): array
    {
        return [
            [
                // Pass to EnumProvider constructor a class that does not exist
                [
                    'Simple' => SimpleEnum::class,
                    'Not-a-class' => '\UnexistingClass',
                ],
            ],
            [
                // Pass to EnumProvider constructor a class that is not an Enum
                [
                    'Simple' => SimpleEnum::class,
                    'Not-an-enum' => \DateTime::class,
                ],
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
