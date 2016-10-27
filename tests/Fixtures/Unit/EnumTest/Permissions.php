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

class Permissions extends FlaggedEnum
{
    const EXECUTE = 1;
    const WRITE = 2;
    const READ = 4;

    const ALL = self::EXECUTE | self::WRITE | self::READ;

    public static function getPossibleValues(): array
    {
        return [
            static::EXECUTE,
            static::WRITE,
            static::READ,
        ];
    }

    public static function getReadables(): array
    {
        return [
            static::EXECUTE => 'Execute',
            static::WRITE => 'Write',
            static::READ => 'Read',

            static::WRITE | static::READ => 'Read & write',
            static::EXECUTE | static::READ => 'Read & execute',
            static::ALL => 'All permissions',
        ];
    }
}
