<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\ReadableEnum;

/**
 * @method static Gender UNKNOW()
 * @method static Gender MALE()
 * @method static Gender FEMALE()
 */
final class Gender extends ReadableEnum
{
    const UNKNOW = 'unknown';
    const MALE = 'male';
    const FEMALE = 'female';

    public static function values(): array
    {
        return [
            self::UNKNOW,
            self::MALE,
            self::FEMALE,
        ];
    }

    public static function readables(): array
    {
        return [
            self::UNKNOW => 'Unknown',
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        ];
    }
}
