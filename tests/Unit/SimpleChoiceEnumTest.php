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
use PHPUnit\Framework\TestCase;

class SimpleChoiceEnumTest extends TestCase
{
    public function testSimpleChoiceEnum()
    {
        $this->assertSame(['foo', 'bar', 'baz'], DummySimpleChoiceEnum::values());
        $this->assertSame([
            ChoiceEnum::FOO => 'Foo',
            ChoiceEnum::BAR => 'Bar',
            ChoiceEnum::BAZ => 'Baz',
        ], DummySimpleChoiceEnum::readables());
    }

    public function testSimpleChoiceEnumWithLabelOverride()
    {
        $this->assertSame(['foo', 'bar', 'baz'], DummySimpleChoiceEnumWithLabelOverride::values());
        $this->assertSame([
            ChoiceEnum::FOO => 'Foo label',
            ChoiceEnum::BAR => 'Bar',
            ChoiceEnum::BAZ => 'Baz',
        ], DummySimpleChoiceEnumWithLabelOverride::readables());
    }

    public function testSimpleChoiceEnumFromDumbEnum()
    {
        $this->assertSame(['foo', 'bar', 'baz'], SimpleChoiceEnumFromDumEnum::values());
        $this->assertSame([
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ], SimpleChoiceEnumFromDumEnum::readables());
    }
}

class DummySimpleChoiceEnum extends SimpleChoiceEnum
{
    const FOO = 'foo';
    const BAR = 'bar';
    const BAZ = 'baz';
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
