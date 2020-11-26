<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\SimpleChoiceEnum;
use Elao\Enum\Tests\Fixtures\Enum\SimpleChoiceEnumFromDumEnum;
use Elao\Enum\Tests\TestCase;

class SimpleChoiceEnumTest extends TestCase
{
    public function testSimpleChoiceEnum(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], DummySimpleChoiceEnum::values());
        self::assertSame([
            ChoiceEnum::FOO => 'Foo',
            ChoiceEnum::BAR => 'Bar',
            ChoiceEnum::BAZ => 'Baz',
        ], DummySimpleChoiceEnum::readables());
    }

    public function testSimpleChoiceEnumWithLabelOverride(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], DummySimpleChoiceEnumWithLabelOverride::values());
        self::assertSame([
            ChoiceEnum::FOO => 'Foo label',
            ChoiceEnum::BAR => 'Bar',
            ChoiceEnum::BAZ => 'Baz',
        ], DummySimpleChoiceEnumWithLabelOverride::readables());
    }

    public function testSimpleChoiceEnumFromDumbEnum(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], SimpleChoiceEnumFromDumEnum::values());
        self::assertSame([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ], SimpleChoiceEnumFromDumEnum::readables());
    }
}

class DummySimpleChoiceEnum extends SimpleChoiceEnum
{
    public const FOO = 'foo';
    public const BAR = 'bar';
    public const BAZ = 'baz';
}

final class DummySimpleChoiceEnumWithLabelOverride extends DummySimpleChoiceEnum
{
    protected static function choices(): array
    {
        return array_replace(parent::choices(), [
            static::FOO => 'Foo label',
        ]);
    }
}
