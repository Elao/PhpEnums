<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Tests\Fixtures\Enum\ExtendedSimpleEnum;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\MathConstant;
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
        $enum = SimpleEnum::get($value);

        $this->assertSame($value, $enum->getValue());
    }

    public function testCallStaticEnumValue()
    {
        $enum = SimpleEnum::SECOND();

        $this->assertSame(2, $enum->getValue());
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage No constant named "FOO" exists in class "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum"
     */
    public function testCallStaticOnInvalidConstantThrowsException()
    {
        SimpleEnum::FOO();
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage "invalid_value" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum" enum.
     */
    public function testExceptionIsRaisedWhenValueIsNotAcceptable()
    {
        SimpleEnum::get('invalid_value');
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage "0" is not an acceptable value
     */
    public function testExceptionIsRaisedWhenValueIsNotAcceptableWithStrictCheck()
    {
        SimpleEnum::get('0');
    }

    public function testEnumsForEqualsWithSameClass()
    {
        $enum = SimpleEnum::get(SimpleEnum::FIRST);

        $this->assertTrue($enum->equals(SimpleEnum::get(SimpleEnum::FIRST)));
        $this->assertFalse($enum->equals(SimpleEnum::get(SimpleEnum::SECOND)));
    }

    public function testEnumsForEqualsWithExtendedClasses()
    {
        $enum = SimpleEnum::get(SimpleEnum::FIRST);

        $this->assertFalse($enum->equals(ExtendedSimpleEnum::get(ExtendedSimpleEnum::FIRST)));
    }

    public function testSameEnumValueActsAsSingleton()
    {
        $this->assertTrue(SimpleEnum::get(SimpleEnum::FIRST) === SimpleEnum::get(SimpleEnum::FIRST));
    }

    public function testInstances()
    {
        $this->assertSame([
            SimpleEnum::get(SimpleEnum::ZERO),
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::SECOND),
        ], SimpleEnum::instances());
    }

    public function testIs()
    {
        $enum = SimpleEnum::get(SimpleEnum::SECOND);

        $this->assertTrue($enum->is(2));
    }

    public function testSerialize()
    {
        $enum = SimpleEnum::get(SimpleEnum::SECOND);
        $expected = sprintf(
            'C:%d:"%s":%d:{%s}',
            strlen(SimpleEnum::class),
            SimpleEnum::class,
            count($enum),
            (string)SimpleEnum::SECOND
        );

        $this->assertEquals($expected, \serialize($enum));
    }

    /**
     * @expectedException \Elao\Enum\Exception\NotSerializableException
     * @expectedExceptionMessage Only values of type "string" or "int" can be serialized
     */
    public function testExceptionIsRaisedWhenSerializingValuesThatAreNotAllowed()
    {
        $enum = MathConstant::get(MathConstant::PI);
        \serialize($enum);
    }

    public function testUnserialize()
    {
        $serializedEnumWithStringValues = sprintf(
            'C:%d:"%s":%d:{%s}',
            strlen(Gender::class),
            Gender::class,
            strlen(Gender::FEMALE),
            Gender::FEMALE
        );
        $serializedEnumWithIntegerValues = sprintf(
            'C:%d:"%s":%d:{%s}',
            strlen(SimpleEnum::class),
            SimpleEnum::class,
            strlen((string)SimpleEnum::SECOND),
            (string)SimpleEnum::SECOND
        );

        $this->assertTrue(Gender::get(Gender::FEMALE)->equals(\unserialize($serializedEnumWithStringValues)));
        $this->assertTrue(SimpleEnum::get(SimpleEnum::SECOND)->equals(\unserialize($serializedEnumWithIntegerValues)));
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage "invalid_value" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum" enum.
     */
    public function testExceptionIsRaisedWhenUnserializingNotExistingValue()
    {
        $serialized = sprintf('C:%d:"%s":13:{invalid_value}', strlen(SimpleEnum::class), SimpleEnum::class);
        \unserialize($serialized);
    }
}
