<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\FlaggedEnum;

/**
 * @method static Permissions EXECUTE()
 * @method static Permissions WRITE()
 * @method static Permissions READ()
 * @method static Permissions ALL()
 */
final class Permissions extends FlaggedEnum
{
    public const EXECUTE = 1;
    public const WRITE = 2;
    public const READ = 4;

    public const ALL = self::EXECUTE | self::WRITE | self::READ;

    public static function values(): array
    {
        return [
            static::EXECUTE,
            static::WRITE,
            static::READ,
        ];
    }

    public static function readables(): array
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
