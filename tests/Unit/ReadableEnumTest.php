<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Tests\Fixtures\Unit\EnumTest\Gender;

class ReadableEnumTest extends \PHPUnit_Framework_TestCase
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
        $enumValue = Gender::create($value);

        $this->assertSame($value, $enumValue->getValue());
        $this->assertSame($expectedReadable, $enumValue->getReadable());
    }

    public function testEnumToString()
    {
        $enumValue = Gender::create(Gender::MALE);

        $this->assertEquals('Male', (string) $enumValue);
    }

    public function testValueCanBeReadabled()
    {
        $this->assertSame('Female', Gender::getReadableFor(Gender::FEMALE));
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     */
    public function testExceptionIsRaisedWhenValueCannotBeReadable()
    {
        Gender::getReadableFor('invalid_value');
    }
}
