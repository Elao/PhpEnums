<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\ChoiceEnumTrait;
use Elao\Enum\Enum;
use Elao\Enum\Exception\LogicException;
use Elao\Enum\FlaggedEnum;
use Elao\Enum\ReadableEnum;
use Elao\Enum\Tests\TestCase;

class ChoiceEnumTraitTest extends TestCase
{
    public function testItProvidesValuesAndReadablesImplementations(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], ChoiceEnum::values());
        self::assertSame([
            ChoiceEnum::FOO => 'Foo label',
            ChoiceEnum::BAR => 'Bar label',
            ChoiceEnum::BAZ => 'Baz label',
        ], ChoiceEnum::readables());
    }

    public function testItFiltersValuesForFlaggedEnumImplementations(): void
    {
        self::assertSame([1, 2, 4], FlaggedEnumWithChoiceEnumTrait::values());
        self::assertSame([
            FlaggedEnumWithChoiceEnumTrait::EXECUTE => 'Execute',
            FlaggedEnumWithChoiceEnumTrait::WRITE => 'Write',
            FlaggedEnumWithChoiceEnumTrait::READ => 'Read',
            FlaggedEnumWithChoiceEnumTrait::WRITE | FlaggedEnumWithChoiceEnumTrait::READ => 'Read & write',
            FlaggedEnumWithChoiceEnumTrait::EXECUTE | FlaggedEnumWithChoiceEnumTrait::READ => 'Read & execute',
            FlaggedEnumWithChoiceEnumTrait::ALL => 'All permissions',
        ], FlaggedEnumWithChoiceEnumTrait::readables());
    }

    public function testValuesThrowsOnNonReadable(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "Elao\Enum\ChoiceEnumTrait" trait is meant to be used by "Elao\Enum\ReadableEnumInterface" implementations, but "Elao\Enum\Tests\Unit\NonReadableEnumWithChoiceEnumTrait" does not implement it.');

        NonReadableEnumWithChoiceEnumTrait::values();
    }

    public function testReadableThrowsOnNonReadable(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "Elao\Enum\ChoiceEnumTrait" trait is meant to be used by "Elao\Enum\ReadableEnumInterface" implementations, but "Elao\Enum\Tests\Unit\NonReadableEnumWithChoiceEnumTrait" does not implement it.');

        NonReadableEnumWithChoiceEnumTrait::readables();
    }
}

final class ChoiceEnum extends ReadableEnum
{
    use ChoiceEnumTrait;

    public const FOO = 'foo';
    public const BAR = 'bar';
    public const BAZ = 'baz';

    protected static function choices(): array
    {
        return [
            static::FOO => 'Foo label',
            static::BAR => 'Bar label',
            static::BAZ => 'Baz label',
        ];
    }
}

final class FlaggedEnumWithChoiceEnumTrait extends FlaggedEnum
{
    use ChoiceEnumTrait;

    public const EXECUTE = 1;
    public const WRITE = 2;
    public const READ = 4;

    public const ALL = self::EXECUTE | self::WRITE | self::READ;

    public static function choices(): array
    {
        return [
            static::EXECUTE => 'Execute',
            static::WRITE => 'Write',
            static::READ => 'Read',
            static::WRITE | static::READ => 'Read & write',
            static::EXECUTE | static::READ => 'Read & execute',
            static::ALL => 'All permissions',
        ];
    }
}

final class NonReadableEnumWithChoiceEnumTrait extends Enum
{
    use ChoiceEnumTrait;

    protected static function choices(): array
    {
        return ['foo' => 'Foo label'];
    }
}
