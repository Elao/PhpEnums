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
 * @method static MathConstant PI()
 * @method static MathConstant MALE()
 * @method static MathConstant FEMALE()
 */
final class MathConstant extends Enum
{
    const PI = 3.14159;
    const EULER = 2.71828;
    const GOLDEN = 1.61803;

    public static function values(): array
    {
        return [
            self::PI,
            self::EULER,
            self::GOLDEN,
        ];
    }
}
