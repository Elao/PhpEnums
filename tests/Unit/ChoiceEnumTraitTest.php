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
use PHPUnit\Framework\TestCase;

class ChoiceEnumTraitTest extends TestCase
{
    public function testItProvidesValuesAndReadablesImplementations()
    {
        $this->assertSame(['foo', 'bar', 'baz'], ChoiceEnum::values());
        $this->assertSame([
            ChoiceEnum::FOO => 'Foo label',
            ChoiceEnum::BAR => 'Bar label',
            ChoiceEnum::BAZ => 'Baz label',
        ], ChoiceEnum::readables());
    }

    public function testItFiltersValuesForFlaggedEnumImplementations()
    {
        $this->assertSame([1, 2, 4], FlaggedEnumWithChoiceEnumTrait::values());
        $this->assertSame([
            FlaggedEnumWithChoiceEnumTrait::EXECUTE => 'Execute',
            FlaggedEnumWithChoiceEnumTrait::WRITE => 'Write',
            FlaggedEnumWithChoiceEnumTrait::READ => 'Read',
            FlaggedEnumWithChoiceEnumTrait::WRITE | FlaggedEnumWithChoiceEnumTrait::READ => 'Read & write',
            FlaggedEnumWithChoiceEnumTrait::EXECUTE | FlaggedEnumWithChoiceEnumTrait::READ => 'Read & execute',
            FlaggedEnumWithChoiceEnumTrait::ALL => 'All permissions',
        ], FlaggedEnumWithChoiceEnumTrait::readables());
    }

    public function testValuesThrowsOnNonReadable()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "Elao\Enum\ChoiceEnumTrait" trait is meant to be used by "Elao\Enum\ReadableEnumInterface" implementations, but "Elao\Enum\Tests\Unit\NonReadableEnumWithChoiceEnumTrait" does not implement it.');

        NonReadableEnumWithChoiceEnumTrait::values();
    }

    public function testReadableThrowsOnNonReadable()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "Elao\Enum\ChoiceEnumTrait" trait is meant to be used by "Elao\Enum\ReadableEnumInterface" implementations, but "Elao\Enum\Tests\Unit\NonReadableEnumWithChoiceEnumTrait" does not implement it.');

        NonReadableEnumWithChoiceEnumTrait::readables();
    }
}

final class ChoiceEnum extends ReadableEnum
{
    use ChoiceEnumTrait;

    const FOO = 'foo';
    const BAR = 'bar';
    const BAZ = 'baz';

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

    const EXECUTE = 1;
    const WRITE = 2;
    const READ = 4;

    const ALL = self::EXECUTE | self::WRITE | self::READ;

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
