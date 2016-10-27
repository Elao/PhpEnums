<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Unit\EnumTest;

use Elao\Enum\FlaggedEnum;

class InvalidFlagsEnum extends FlaggedEnum
{
    const FIRST = 1;
    const SECOND = 2;
    const INVALID = 3;

    public static function getReadables(): array
    {
        return [
            static::FIRST => 'First',
            static::SECOND => 'Second',
            static::INVALID => 'Invalid',
        ];
    }
    public static function getPossibleValues(): array
    {
        return [
            static::FIRST,
            static::SECOND,
            static::INVALID,
        ];
    }
}
