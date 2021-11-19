<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Exception\InvalidArgumentException;
use Elao\Enum\FlagBag;
use Elao\Enum\FlagEnumInterface;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;

class FlagBagTest extends TestCase
{
    public function testConstructThrowsExceptionOnInvalidEnumType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"Elao\Enum\Tests\Fixtures\Enum\Suit" does not implements "Elao\Enum\FlagEnumInterface"');

        new FlagBag(Suit::class, 3);
    }

    public function testConstructThrowsExceptionOnInvalidBitsCombination(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('99 is not a valid bits combination for "Elao\Enum\Tests\Fixtures\Enum\Permissions"');

        new FlagBag(Permissions::class, 99);
    }

    public function acceptableValueProvider(): array
    {
        return [
            [Permissions::class, FlagBag::NONE, true],
            [Permissions::class, Permissions::Execute->value, true],
            [Permissions::class, Permissions::Write->value, true],
            [Permissions::class, Permissions::Read->value, true],
            [Permissions::class, Permissions::Read->value | Permissions::Write->value, true],
//            [Permissions::class, Permissions::ALL->value, true],
            [Permissions::class, 99, false],
        ];
    }

    /**
     * @dataProvider acceptableValueProvider
     */
    public function testAcceptableValues(string $enumType, int $value, bool $result): void
    {
        self::assertSame(
            $result,
            FlagBag::accepts($enumType, $value),
            sprintf('->accepts() returns %s if the value %d.', $result ? 'true' : 'false', $value)
        );
    }

    public function from valid values(): array
    {
        return [
            [Permissions::class],
            [Permissions::class, Permissions::Execute],
            [Permissions::class, Permissions::Write],
            [Permissions::class, Permissions::Read],
            [Permissions::class, Permissions::Read, Permissions::Write],
        ];
    }

    /**
     * @dataProvider from valid values
     */
    public function testFrom(string $enumType, FlagEnumInterface ...$flags)
    {
        FlagBag::from($enumType, ...$flags);

        $this->addToAssertionCount(1);
    }

    public function testGetFlags(): void
    {
        $bag = new FlagBag(Permissions::class, Permissions::Write->value, Permissions::Read->value);

        self::assertEquals([Permissions::Write, Permissions::Read], $bag->getFlags());
    }

    public function testGetBits(): void
    {
        $bag = new FlagBag(Permissions::class, Permissions::Write->value, Permissions::Read->value);

        self::assertSame([Permissions::Write->value, Permissions::Read->value], $bag->getBits());
    }

    public function testWithBits(): void
    {
        $original = FlagBag::from(Permissions::Read);
        $result = $original->withBits(Permissions::Write->value, Permissions::Execute->value);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasBits(Permissions::Execute->value));
        self::assertTrue($result->hasBits(Permissions::Write->value));
        self::assertTrue($result->hasBits(Permissions::Read->value));
    }

    public function testWithBitsThrowsOnInvalidBits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"99" is not a valid flags combination for "Elao\Enum\Tests\Fixtures\Enum\Permissions"');

        $bag = new FlagBag(Permissions::class);
        $bag->withBits(99);
    }

    public function testWithoutBits(): void
    {
        $original = FlagBag::from(Permissions::Read, Permissions::Write, Permissions::Execute);
        $result = $original->withoutBits(Permissions::Read->value, Permissions::Write->value);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasBits(Permissions::Execute->value));
        self::assertFalse($result->hasBits(Permissions::Write->value));
        self::assertFalse($result->hasBits(Permissions::Read->value));
    }

    public function testWithoutBitsThrowsOnInvalidBits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"99" is not a valid flags combination for "Elao\Enum\Tests\Fixtures\Enum\Permissions"');

        $bag = new FlagBag(Permissions::class);
        $bag->withoutBits(99);
    }

    public function testWithoutAnyBits(): void
    {
        $original = FlagBag::from(Permissions::Read, Permissions::Write, Permissions::Execute);
        $result = $original->withoutBits(Permissions::Read->value, Permissions::Write->value, Permissions::Execute->value);

        self::assertCount(0, $result->getBits());
    }

    public function testWithFlags(): void
    {
        $original = FlagBag::from(Permissions::Read);
        $result = $original->withFlags(Permissions::Write, Permissions::Execute);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasFlags(Permissions::Execute));
        self::assertTrue($result->hasFlags(Permissions::Write));
        self::assertTrue($result->hasFlags(Permissions::Read));
    }

    public function testWithoutFlags(): void
    {
        $original = FlagBag::from(Permissions::Read, Permissions::Write, Permissions::Execute);
        $result = $original->withoutFlags(Permissions::Read, Permissions::Write);

        self::assertNotSame($original, $result);
        self::assertTrue($result->hasFlags(Permissions::Execute));
        self::assertFalse($result->hasFlags(Permissions::Write));
        self::assertFalse($result->hasFlags(Permissions::Read));
    }
}
