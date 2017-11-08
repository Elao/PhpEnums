<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Tests\Fixtures\Enum\Gender;
use PHPUnit\Framework\TestCase;

class ReadableEnumTest extends TestCase
{
    public function enumValuesProvider()
    {
        return [
            [Gender::MALE, 'Male'],
            [Gender::FEMALE, 'Female'],
        ];
    }

    /**
     * @dataProvider enumValuesProvider
     */
    public function testCreateEnumValue($value, $expectedReadable)
    {
        $enumValue = Gender::get($value);

        $this->assertSame($value, $enumValue->getValue());
        $this->assertSame($expectedReadable, $enumValue->getReadable());
    }

    public function testEnumToString()
    {
        $enumValue = Gender::get(Gender::MALE);

        $this->assertSame('Male', (string) $enumValue);
    }

    public function testValueCanBeReadabled()
    {
        $this->assertSame('Female', Gender::readableFor(Gender::FEMALE));
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     */
    public function testExceptionIsRaisedWhenValueCannotBeReadable()
    {
        Gender::readableFor('invalid_value');
    }
}
