<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Exception\InvalidValueException;
use Elao\Enum\Tests\Fixtures\Enum\ExtendedSimpleEnum;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
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

    public function testCallStaticOnInvalidConstantThrowsException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('No constant named "FOO" exists in class "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum"');

        SimpleEnum::FOO();
    }

    public function testExceptionIsRaisedWhenValueIsNotAcceptable()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('"invalid_value" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum" enum.');

        SimpleEnum::get('invalid_value');
    }

    public function testExceptionIsRaisedWhenValueIsNotAcceptableWithStrictCheck()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('"0" is not an acceptable value');

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
}
