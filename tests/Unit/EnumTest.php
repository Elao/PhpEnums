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
use Elao\Enum\Tests\TestCase;

class EnumTest extends TestCase
{
    public function enumValuesProvider(): array
    {
        return [
            [1],
            [SimpleEnum::SECOND],
        ];
    }

    /**
     * @dataProvider enumValuesProvider
     */
    public function testCreateEnumValue($value): void
    {
        $enum = SimpleEnum::get($value);

        self::assertSame($value, $enum->getValue());
    }

    public function testCallStaticEnumValue(): void
    {
        $enum = SimpleEnum::SECOND();

        self::assertSame(2, $enum->getValue());
    }

    public function testCallStaticOnInvalidConstantThrowsException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('No constant named "FOO" exists in class "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum"');

        SimpleEnum::FOO();
    }

    public function testExceptionIsRaisedWhenValueIsNotAcceptable(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('"invalid_value" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum" enum.');

        SimpleEnum::get('invalid_value');
    }

    public function testExceptionIsRaisedWhenValueIsNotAcceptableWithStrictCheck(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('"0" is not an acceptable value');

        SimpleEnum::get('0');
    }

    public function testEnumsForEqualsWithSameClass(): void
    {
        $enum = SimpleEnum::get(SimpleEnum::FIRST);

        self::assertTrue($enum->equals(SimpleEnum::get(SimpleEnum::FIRST)));
        self::assertFalse($enum->equals(SimpleEnum::get(SimpleEnum::SECOND)));
    }

    public function testEnumsForEqualsWithExtendedClasses(): void
    {
        $enum = SimpleEnum::get(SimpleEnum::FIRST);

        self::assertFalse($enum->equals(ExtendedSimpleEnum::get(ExtendedSimpleEnum::FIRST)));
    }

    public function testSameEnumValueActsAsSingleton(): void
    {
        self::assertSame(SimpleEnum::get(SimpleEnum::FIRST), SimpleEnum::get(SimpleEnum::FIRST));
    }

    public function testInstances(): void
    {
        self::assertSame([
            SimpleEnum::get(SimpleEnum::ZERO),
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::SECOND),
        ], SimpleEnum::instances());
    }

    public function testIs(): void
    {
        $enum = SimpleEnum::get(SimpleEnum::SECOND);

        self::assertTrue($enum->is(2));
    }
}
