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
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\TestCase;

class ReadableEnumTest extends TestCase
{
    public function enumValuesProvider(): iterable
    {
        yield [Gender::MALE, 'Male'];
        yield [Gender::FEMALE, 'Female'];
    }

    /**
     * @dataProvider enumValuesProvider
     */
    public function testCreateEnumValue($value, $expectedReadable): void
    {
        $enumValue = Gender::get($value);

        self::assertSame($value, $enumValue->getValue());
        self::assertSame($expectedReadable, $enumValue->getReadable());
    }

    public function testEnumToString(): void
    {
        $enumValue = Gender::get(Gender::MALE);

        self::assertSame('Male', (string) $enumValue);
    }

    public function testValueCanBeReadabled(): void
    {
        self::assertSame('Female', Gender::readableFor(Gender::FEMALE));
    }

    public function testExceptionIsRaisedWhenValueCannotBeReadable(): void
    {
        $this->expectException(InvalidValueException::class);

        Gender::readableFor('invalid_value');
    }
}
