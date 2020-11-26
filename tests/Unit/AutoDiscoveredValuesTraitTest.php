<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;
use Elao\Enum\Exception\LogicException;
use Elao\Enum\FlaggedEnum;
use Elao\Enum\Tests\Fixtures\Enum\DumbEnum;
use Elao\Enum\Tests\Fixtures\Enum\Php71AutoDiscoveredEnum;
use Elao\Enum\Tests\TestCase;

class AutoDiscoveredValuesTraitTest extends TestCase
{
    public function testItAutoDiscoveredValuesBasedOnAvailableConstants(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], AutoDiscoveredEnum::values());
    }

    public function testPHP71ItAutoDiscoveredValuesBasedOnAvailableConstants(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], Php71AutoDiscoveredEnum::values());
    }

    public function testItAutoDiscoveredValuesBasedOnAvailableBitFlagConstants(): void
    {
        self::assertSame([1, 2, 4], AutoDiscoveredFlaggedEnum::values());
    }

    public function testThrowsOnChoicesMisuses(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Method "Elao\Enum\AutoDiscoveredValuesTrait::choices" is only meant to be used when using the "Elao\Enum\ChoiceEnumTrait" trait which is not used in "Elao\Enum\Tests\Unit\AutoDiscoveredEnumMisusingChoices"');

        AutoDiscoveredEnumMisusingChoices::foo();
    }

    public function testItAutoDiscoversValuesFromDumbEnum(): void
    {
        self::assertSame(['foo', 'bar', 'baz'], AutoDiscoveredDumbEnum::values());
    }

    public function testItRemovesAutoDiscoveredNonUniqueIdentifiersWithContiguousIndices(): void
    {
        self::assertSame([0 => 'foo', 1 => 'bar'], AutoDiscoveredNonUniqueEnumIndentifiers::values());
    }
}

final class AutoDiscoveredEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const FOO = 'foo';
    public const BAR = 'bar';
    public const BAZ = 'baz';

    public const NOT_AN_INT_NOR_STRING = ['foo'];
}

final class AutoDiscoveredFlaggedEnum extends FlaggedEnum
{
    use AutoDiscoveredValuesTrait;

    public const FOO = 1;
    public const BAR = 2;
    public const BAZ = 4;

    public const NOT_A_BIT_FLAG = 3;
    public const NOT_EVEN_AN_INT = 'not_even_an_int';
}

final class AutoDiscoveredEnumMisusingChoices extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const FOO = 'foo';

    public static function foo(): void
    {
        self::choices();
    }
}

final class AutoDiscoveredDumbEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const BAZ = 'baz';

    protected static function getDiscoveredClasses(): array
    {
        return [self::class, DumbEnum::class];
    }
}

final class AutoDiscoveredNonUniqueEnumIndentifiers extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const FOO = 'foo';
    public const FOO_ALIAS = 'foo';
    public const BAR = 'bar';
}
