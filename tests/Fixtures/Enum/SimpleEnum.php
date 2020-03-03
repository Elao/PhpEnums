<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\Enum;

/**
 * @method static SimpleEnum ZERO()
 * @method static SimpleEnum FIRST()
 * @method static SimpleEnum SECOND()
 */
class SimpleEnum extends Enum
{
    const ZERO = 0;
    const FIRST = 1;
    const SECOND = 2;

    public static function values(): array
    {
        return [
            self::ZERO,
            self::FIRST,
            self::SECOND,
        ];
    }

    public static function allowedValues(): array
    {
        return [
            self::get(self::FIRST),
            self::get(self::SECOND),
        ];
    }
}
