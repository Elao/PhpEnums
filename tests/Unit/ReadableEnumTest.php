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

use Elao\Enum\Exception\NameException;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use Elao\Enum\Tests\Fixtures\Enum\UnitSuit;
use PHPUnit\Framework\TestCase;

class ReadableEnumTest extends TestCase
{
    public function testReadableForName(): void
    {
        self::assertSame('suit.clubs', Suit::readableForName(Suit::Clubs->name));
    }

    public function testReadableForNameExceptionOnInvalidName(): void
    {
        $this->expectException(NameException::class);

        Suit::readableForName('invalidName');
    }

    public function testReadableForValue(): void
    {
        self::assertSame('suit.clubs', Suit::readableForValue(Suit::Clubs->value));
    }

    public function testReadableForValueThrowOnUnitEnum(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot call method "Elao\Enum\ReadableEnumTrait::readableForValue" on non-backed enum "Elao\Enum\Tests\Fixtures\Enum\UnitSuit');

        self::assertSame('suit.clubs', UnitSuit::readableForValue('C'));
    }

    public function testReadableForValueExceptionOnInvalidName(): void
    {
        $this->expectException(\ValueError::class);

        Suit::readableForValue('invalidValue');
    }
}
