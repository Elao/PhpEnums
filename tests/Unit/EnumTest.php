<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Tests\Fixtures\Enum\ExtendedSimpleEnum;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function enumValuesProvider()
    {
        return [
            [1],
            [SimpleEnum::SECOND],
        ];
    }

    /**
     * @dataProvider enumValuesProvider
     */
    public function testCreateEnumValue($value)
    {
        $enumValue = SimpleEnum::create($value);

        $this->assertSame($value, $enumValue->getValue());
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage "invalid_value" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum" enum.
     */
    public function testExceptionIsRaisedWhenValueIsNotAcceptable()
    {
        SimpleEnum::create('invalid_value');
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage "0" is not an acceptable value
     */
    public function testExceptionIsRaisedWhenValueIsNotAcceptableWithStrictCheck()
    {
        SimpleEnum::create('0');
    }

    public function testEnumsForEqualsWithSameClass()
    {
        $enum = SimpleEnum::create(SimpleEnum::FIRST);

        $this->assertTrue($enum->equals(SimpleEnum::create(SimpleEnum::FIRST)));
        $this->assertFalse($enum->equals(SimpleEnum::create(SimpleEnum::SECOND)));
    }

    public function testEnumsForEqualsWithExtendedClasses()
    {
        $enum = SimpleEnum::create(SimpleEnum::FIRST);

        $this->assertFalse($enum->equals(ExtendedSimpleEnum::create(ExtendedSimpleEnum::FIRST)));
    }

    public function testSameEnumValueActsAsSingleton()
    {
        $this->assertTrue(SimpleEnum::create(SimpleEnum::FIRST) === SimpleEnum::create(SimpleEnum::FIRST));
    }

    public function testGetPossibleInstances()
    {
        $this->assertSame([
            SimpleEnum::create(SimpleEnum::ZERO),
            SimpleEnum::create(SimpleEnum::FIRST),
            SimpleEnum::create(SimpleEnum::SECOND),
        ], SimpleEnum::instances());
    }

    public function testIs()
    {
        $enum = SimpleEnum::create(SimpleEnum::SECOND);

        $this->assertTrue($enum->is(2));
    }
}
