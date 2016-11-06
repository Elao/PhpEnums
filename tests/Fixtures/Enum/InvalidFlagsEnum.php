<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\FlaggedEnum;

class InvalidFlagsEnum extends FlaggedEnum
{
    const FIRST = 1;
    const SECOND = 2;
    const INVALID = 3;

    public static function readables(): array
    {
        return [
            static::FIRST => 'First',
            static::SECOND => 'Second',
            static::INVALID => 'Invalid',
        ];
    }

    public static function values(): array
    {
        return [
            static::FIRST,
            static::SECOND,
            static::INVALID,
        ];
    }
}
