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

use Elao\Enum\Tests\Fixtures\Enum\SuitWithCustomCase;
use Elao\Enum\Tests\Fixtures\Enum\SuitWithExtras;
use Elao\Enum\Tests\IterableAssertionsTrait;
use PHPUnit\Framework\TestCase;

class EnumExtrasTest extends TestCase
{
    use IterableAssertionsTrait;

    public function testGetExtra(): void
    {
        self::assertSame('black', SuitWithExtras::Clubs->getExtra('color'));
        self::assertSame('fa-club', SuitWithExtras::Clubs->getExtra('icon'));

        self::assertSame('red', SuitWithExtras::Diamonds->getExtra('color'));
        self::assertSame('fa-diamond', SuitWithExtras::Diamonds->getExtra('icon'));

        self::assertSame('value', SuitWithExtras::Hearts->getExtra('only-for-hearts'));
    }

    public function testGetExtraWithCustomCase(): void
    {
        self::assertSame('black', SuitWithCustomCase::Clubs->getExtra('color'));
        self::assertSame('fa-club', SuitWithCustomCase::Clubs->getExtra('icon'));

        self::assertSame('red', SuitWithCustomCase::Diamonds->getExtra('color'));
        self::assertSame('fa-diamond', SuitWithCustomCase::Diamonds->getExtra('icon'));
    }

    public function testGetExtraReturnsNullOnMissingKey(): void
    {
        self::assertNull(SuitWithExtras::Clubs->getExtra('missing-key'));
        self::assertNull(SuitWithExtras::Clubs->getExtra('only-for-hearts'));
    }

    public function testGetExtraCanThrow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No value for extra "missing-key" for enum case Elao\Enum\Tests\Fixtures\Enum\SuitWithExtras::Clubs');

        self::assertSame('black', SuitWithExtras::Clubs->getExtra('missing-key', true));
    }

    public function testExtrasCanBeIteratedWithEnumCaseAsKeys(): void
    {
        self::assertIterablesMatch((static function () {
            yield SuitWithExtras::Hearts => 'red';
            yield SuitWithExtras::Diamonds => 'red';
            yield SuitWithExtras::Clubs => 'black';
            yield SuitWithExtras::Spades => 'black';
        })(), SuitWithExtras::extras('color'));
    }

    public function testWithCustomCaseExtrasCanBeIteratedWithEnumCaseAsKeys(): void
    {
        self::assertIterablesMatch((static function () {
            yield SuitWithCustomCase::Hearts => 'red';
            yield SuitWithCustomCase::Diamonds => 'red';
            yield SuitWithCustomCase::Clubs => 'black';
            yield SuitWithCustomCase::Spades => 'black';
        })(), SuitWithCustomCase::extras('color'));
    }

    public function testExtrasAreNullOnMissingKey(): void
    {
        self::assertIterablesMatch((static function () {
            yield SuitWithExtras::Hearts => 'value';
            yield SuitWithExtras::Diamonds => null;
            yield SuitWithExtras::Clubs => null;
            yield SuitWithExtras::Spades => null;
        })(), SuitWithExtras::extras('only-for-hearts'));
    }

    public function testExtrasCanThrowOnMissingKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No value for extra "only-for-hearts" for enum case Elao\Enum\Tests\Fixtures\Enum\SuitWithExtras::Diamonds');

        self::iterates(SuitWithExtras::extras('only-for-hearts', true));
    }
}
