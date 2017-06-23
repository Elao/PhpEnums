<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;
use Elao\Enum\FlaggedEnum;

class AutoDiscoveredValuesTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testItAutoDiscoveredValuesBasedOnAvailableConstants()
    {
        $this->assertSame(['foo', 'bar', 'baz'], AutoDiscoveredEnum::values());
    }

    public function testItAutoDiscoveredValuesBasedOnAvailableBitFlagConstants()
    {
        $this->assertSame([1, 2, 4], AutoDiscoveredFlaggedEnum::values());
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
}
