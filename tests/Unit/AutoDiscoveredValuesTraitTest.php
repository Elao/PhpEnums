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
use Elao\Enum\FlaggedEnum;
use Elao\Enum\Tests\Fixtures\Enum\Php71AutoDiscoveredEnum;
use PHPUnit\Framework\TestCase;

class AutoDiscoveredValuesTraitTest extends TestCase
{
    public function testItAutoDiscoveredValuesBasedOnAvailableConstants()
    {
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'], AutoDiscoveredEnum::values());
    }

    /**
     * @requires PHP 7.1
     */
    public function testPHP71ItAutoDiscoveredValuesBasedOnAvailableConstants()
    {
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'], Php71AutoDiscoveredEnum::values());
    }

    public function testItAutoDiscoveredValuesBasedOnAvailableBitFlagConstants()
    {
        $this->assertSame([1 => 1, 2 => 2, 4 => 4], AutoDiscoveredFlaggedEnum::values());
    }
}

final class AutoDiscoveredEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    const FOO = 'foo';
    const BAR = 'bar';
    const BAZ = 'baz';
}

final class AutoDiscoveredFlaggedEnum extends FlaggedEnum
{
    use AutoDiscoveredValuesTrait;

    const FOO = 1;
    const BAR = 2;
    const BAZ = 4;

    const NOT_A_BIT_FLAG = 3;
    const NOT_EVEN_AN_INT = 'not_even_an_int';
}
