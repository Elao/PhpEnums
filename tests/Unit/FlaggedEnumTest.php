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
use Elao\Enum\Exception\LogicException;
use Elao\Enum\Tests\Fixtures\Enum\AlarmScheduleType;
use Elao\Enum\Tests\Fixtures\Enum\InvalidFlagsEnum;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use PHPUnit\Framework\TestCase;

class FlaggedEnumTest extends TestCase
{
    public function testGetThrowExceptionWhenValueIsNotInteger()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('"1" is not an acceptable value');

        Permissions::get('1');
    }

    public function acceptableValueProvider()
    {
        return [
            [Permissions::NONE, true],
            [Permissions::EXECUTE, true],
            [Permissions::WRITE, true],
            [Permissions::READ, true],
            [Permissions::READ | Permissions::WRITE, true],
            [Permissions::ALL, true],
            [99, false],
            ['4', false],
        ];
    }

    /**
     * @dataProvider acceptableValueProvider
     */
    public function testAcceptableValue($value, $result)
    {
        $this->assertSame(
            $result,
            Permissions::accepts($value),
            sprintf('->accepts() returns %s if the value %d.', $result ? 'true' : 'false', $value)
        );
    }

    public function testThrowExceptionWhenBitmaskIsInvalid()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Possible value 3 of the enumeration "Elao\Enum\Tests\Fixtures\Enum\InvalidFlagsEnum" is not a bit flag.');

        InvalidFlagsEnum::get(InvalidFlagsEnum::FIRST);
    }

    public function testSameEnumValueActsAsSingleton()
    {
        $this->assertSame(Permissions::get(Permissions::NONE), Permissions::get(Permissions::NONE));
        $this->assertSame(Permissions::get(Permissions::READ), Permissions::get(Permissions::READ));
        $all = Permissions::get(Permissions::ALL);
        $this->assertSame($all, Permissions::get(Permissions::READ | Permissions::WRITE | Permissions::EXECUTE));
        $this->assertSame(
            $all->withoutFlags(Permissions::READ),
            Permissions::get(Permissions::WRITE | Permissions::EXECUTE)
        );
    }

    public function testGetFlagsOfValue()
    {
        $value = Permissions::get(Permissions::NONE | Permissions::WRITE | Permissions::READ);

        $this->assertSame([Permissions::WRITE, Permissions::READ], $value->getFlags());
    }

    public function testSingleFlagIsReadable()
    {
        $this->assertSame('Execute', Permissions::readableFor(Permissions::EXECUTE));

        $instance = Permissions::get(Permissions::EXECUTE);

        $this->assertSame('Execute', $instance->getReadable());
    }

    public function testMultipleFlagsAreReadable()
    {
        $this->assertSame(
            'Execute; Write',
            Permissions::readableFor(Permissions::EXECUTE | Permissions::WRITE)
        );

        $instance = Permissions::get(Permissions::EXECUTE | Permissions::WRITE);

        $this->assertSame('Execute; Write', $instance->getReadable());
    }

    public function testFlagsCombinationCanHaveOwnReadable()
    {
        $this->assertSame(
            'Read & write',
            Permissions::readableFor(Permissions::READ | Permissions::WRITE)
        );

        $instance = Permissions::get(Permissions::ALL);

        $this->assertSame('All permissions', $instance->getReadable());
    }

    public function testNoneCanBeReadable()
    {
        $this->assertSame('None', Permissions::readableFor(Permissions::NONE));

        $instance = Permissions::get(Permissions::NONE);

        $this->assertSame('None', $instance->getReadable());
    }

    public function testReadableSeparatorCanBeChanged()
    {
        $this->assertSame(
            'Execute | Write',
            Permissions::readableFor(Permissions::EXECUTE | Permissions::WRITE, ' | ')
        );
        $instance = Permissions::get(Permissions::EXECUTE | Permissions::WRITE);
        $this->assertSame('Execute | Write', $instance->getReadable(' | '));
    }

    public function testHasBaseReadableImplementation()
    {
        $this->assertSame([
            1 => 'Monday morning',
            2 => 'Monday afternoon',
            4 => 'Tuesday morning',
            8 => 'Tuesday afternoon',
            16 => 'Wednesday morning',
            32 => 'Wednesday afternoon',
            64 => 'Thursday morning',
            128 => 'Thursday afternoon',
            256 => 'Friday morning',
            512 => 'Friday afternoon',
            1024 => 'Saturday morning',
            2048 => 'Saturday afternoon',
            4096 => 'Sunday morning',
            8192 => 'Sunday afternoon',
        ], AlarmScheduleType::readables());
    }

    public function testWithFlags()
    {
        $original = Permissions::get(Permissions::READ);
        $result = $original->withFlags(Permissions::WRITE | Permissions::EXECUTE);

        $this->assertNotSame($original, $result);
        $this->assertTrue($result->hasFlag(Permissions::EXECUTE));
        $this->assertTrue($result->hasFlag(Permissions::WRITE));
        $this->assertTrue($result->hasFlag(Permissions::READ));
    }

    public function testThrowExceptionWhenWithInvalidFlags()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('8 is not an acceptable value');

        $value = Permissions::get(Permissions::READ);
        $value->withFlags(Permissions::ALL + 1);
    }

    public function testWithoutFlags()
    {
        $original = Permissions::get(Permissions::ALL);
        $result = $original->withoutFlags(Permissions::READ | Permissions::WRITE);

        $this->assertNotSame($original, $result);
        $this->assertTrue($result->hasFlag(Permissions::EXECUTE));
        $this->assertFalse($result->hasFlag(Permissions::READ));
        $this->assertFalse($result->hasFlag(Permissions::WRITE));
    }

    public function testWithoutAnyFlag()
    {
        $original = Permissions::get(Permissions::ALL);
        $result = $original->withoutFlags(Permissions::ALL);
        $this->assertCount(0, $result->getFlags());
        $this->assertSame(Permissions::NONE, $result->getValue());
    }

    public function testThrowExceptionWhenInvalidFlagsRemoved()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('99 is not an acceptable value');

        $value = Permissions::get(Permissions::ALL);
        $value->withoutFlags(99);
    }

    public function testInstances()
    {
        $this->assertSame([
            Permissions::get(Permissions::EXECUTE),
            Permissions::get(Permissions::WRITE),
            Permissions::get(Permissions::READ),
        ], Permissions::instances());
    }
}
