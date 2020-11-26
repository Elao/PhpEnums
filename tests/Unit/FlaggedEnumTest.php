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
use Elao\Enum\Tests\TestCase;

class FlaggedEnumTest extends TestCase
{
    public function testGetThrowExceptionWhenValueIsNotInteger(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('"1" is not an acceptable value');

        Permissions::get('1');
    }

    public function acceptableValueProvider(): array
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
    public function testAcceptableValue($value, $result): void
    {
        self::assertSame(
            $result,
            Permissions::accepts($value),
            sprintf('->accepts() returns %s if the value %d.', $result ? 'true' : 'false', $value)
        );
    }

    public function testThrowExceptionWhenBitmaskIsInvalid(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Possible value 3 of the enumeration "Elao\Enum\Tests\Fixtures\Enum\InvalidFlagsEnum" is not a bit flag.');

        InvalidFlagsEnum::get(InvalidFlagsEnum::FIRST);
    }

    public function testSameEnumValueActsAsSingleton(): void
    {
        self::assertSame(Permissions::get(Permissions::NONE), Permissions::get(Permissions::NONE));
        self::assertSame(Permissions::get(Permissions::READ), Permissions::get(Permissions::READ));
        $all = Permissions::get(Permissions::ALL);
        self::assertSame($all, Permissions::get(Permissions::READ | Permissions::WRITE | Permissions::EXECUTE));
        self::assertSame(
            $all->withoutFlags(Permissions::READ),
            Permissions::get(Permissions::WRITE | Permissions::EXECUTE)
        );
    }

    public function testGetFlagsOfValue(): void
    {
        $value = Permissions::get(Permissions::NONE | Permissions::WRITE | Permissions::READ);

        self::assertSame([Permissions::WRITE, Permissions::READ], $value->getFlags());
    }

    public function testSingleFlagIsReadable(): void
    {
        self::assertSame('Execute', Permissions::readableFor(Permissions::EXECUTE));

        $instance = Permissions::get(Permissions::EXECUTE);

        self::assertSame('Execute', $instance->getReadable());
    }

    public function testMultipleFlagsAreReadable(): void
    {
        self::assertSame(
            'Execute; Write',
            Permissions::readableFor(Permissions::EXECUTE | Permissions::WRITE)
        );

        $instance = Permissions::get(Permissions::EXECUTE | Permissions::WRITE);

        self::assertSame('Execute; Write', $instance->getReadable());
    }

    public function testFlagsCombinationCanHaveOwnReadable(): void
    {
        self::assertSame(
            'Read & write',
            Permissions::readableFor(Permissions::READ | Permissions::WRITE)
        );

        $instance = Permissions::get(Permissions::ALL);

        self::assertSame('All permissions', $instance->getReadable());
    }

    public function testNoneCanBeReadable(): void
    {
        self::assertSame('None', Permissions::readableFor(Permissions::NONE));

        $instance = Permissions::get(Permissions::NONE);

        self::assertSame('None', $instance->getReadable());
    }

    public function testReadableSeparatorCanBeChanged(): void
    {
        self::assertSame(
            'Execute | Write',
            Permissions::readableFor(Permissions::EXECUTE | Permissions::WRITE, ' | ')
        );
        $instance = Permissions::get(Permissions::EXECUTE | Permissions::WRITE);
        self::assertSame('Execute | Write', $instance->getReadable(' | '));
    }

    public function testHasBaseReadableImplementation(): void
    {
        self::assertSame([
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

    public function testWithFlags(): void
    {
        $original = Permissions::get(Permissions::READ);
        $result = $original->withFlags(Permissions::WRITE | Permissions::EXECUTE);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasFlag(Permissions::EXECUTE));
        self::assertTrue($result->hasFlag(Permissions::WRITE));
        self::assertTrue($result->hasFlag(Permissions::READ));
    }

    public function testThrowExceptionWhenWithInvalidFlags(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('8 is not an acceptable value');

        $value = Permissions::get(Permissions::READ);
        $value->withFlags(Permissions::ALL + 1);
    }

    public function testWithoutFlags(): void
    {
        $original = Permissions::get(Permissions::ALL);
        $result = $original->withoutFlags(Permissions::READ | Permissions::WRITE);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasFlag(Permissions::EXECUTE));
        self::assertFalse($result->hasFlag(Permissions::READ));
        self::assertFalse($result->hasFlag(Permissions::WRITE));
    }

    public function testWithoutAnyFlag(): void
    {
        $original = Permissions::get(Permissions::ALL);
        $result = $original->withoutFlags(Permissions::ALL);
        self::assertCount(0, $result->getFlags());
        self::assertSame(Permissions::NONE, $result->getValue());
    }

    public function testThrowExceptionWhenInvalidFlagsRemoved(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('99 is not an acceptable value');

        $value = Permissions::get(Permissions::ALL);
        $value->withoutFlags(99);
    }

    public function testInstances(): void
    {
        self::assertSame([
            Permissions::get(Permissions::EXECUTE),
            Permissions::get(Permissions::WRITE),
            Permissions::get(Permissions::READ),
        ], Permissions::instances());
    }
}
