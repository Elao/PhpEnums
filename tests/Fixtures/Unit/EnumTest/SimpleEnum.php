<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Unit\EnumTest;

use Elao\Enum\Enum;

class SimpleEnum extends Enum
{
    const ZERO = 0;
    const FIRST = 1;
    const SECOND = 2;

    public static function getPossibleValues(): array
    {
        return [self::ZERO, self::FIRST, self::SECOND];
    }
}
