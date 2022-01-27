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
use Elao\Enum\Tests\Fixtures\Enum\SuitWithAttributes;
use Elao\Enum\Tests\Fixtures\Enum\SuitWithAttributesMissingAttribute;
use Elao\Enum\Tests\Fixtures\Enum\SuitWithAttributesMissingLabel;
use Elao\Enum\Tests\IterableAssertionsTrait;
use PHPUnit\Framework\TestCase;

class ReadableEnumFromAttributesTest extends TestCase
{
    use IterableAssertionsTrait;

    public function testReadableForName(): void
    {
        self::assertSame('suit.clubs', SuitWithAttributes::readableForName(SuitWithAttributes::Clubs->name));
    }

    public function testReadableForNameExceptionOnInvalidName(): void
    {
        $this->expectException(NameException::class);

        SuitWithAttributes::readableForName('invalidName');
    }

    public function testReadableForValue(): void
    {
        self::assertSame('suit.clubs', SuitWithAttributes::readableForValue(Suit::Clubs->value));
    }

    public function testReadableForValueExceptionOnInvalidName(): void
    {
        $this->expectException(\ValueError::class);

        SuitWithAttributes::readableForValue('invalidValue');
    }

    public function testMissingAttributeThrows(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('enum "Elao\Enum\Tests\Fixtures\Enum\SuitWithAttributesMissingAttribute" using the "Elao\Enum\ReadableEnumTrait" trait must define a "Elao\Enum\Attribute\EnumCase" attribute on every cases. Case "Clubs" is missing one.');

        self::iterates(SuitWithAttributesMissingAttribute::readables());
    }

    public function testMissingLabelThrows(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('enum "Elao\Enum\Tests\Fixtures\Enum\SuitWithAttributesMissingLabel" using the "Elao\Enum\ReadableEnumTrait" trait must define a label using the "Elao\Enum\Attribute\EnumCase" attribute on every cases. Case "Clubs" is missing a label.');

        self::iterates(SuitWithAttributesMissingLabel::readables());
    }

    public function testReadablesCanBeIteratedWithEnumCaseAsKeys(): void
    {
        self::assertIterablesMatch((static function () {
            yield SuitWithAttributes::Hearts => 'suit.hearts';
            yield SuitWithAttributes::Diamonds => 'suit.diamonds';
            yield SuitWithAttributes::Clubs => 'suit.clubs';
            yield SuitWithAttributes::Spades => 'suit.spades';
        })(), SuitWithAttributes::readables());
    }
}
