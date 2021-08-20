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
 * @method static NeedsEscapingEnum APOSTROPHE()
 * @method static NeedsEscapingEnum FORWARD_SLASH()
 */
class NeedsEscapingEnum extends ReadableEnum
{
    public const APOSTROPHE = 'apostrophe';
    public const FORWARD_SLASH = 'forward_slash';

    public static function values(): array
    {
        return [
            self::APOSTROPHE,
            self::FORWARD_SLASH,
        ];
    }

    public static function readables(): array
    {
        return [
            self::APOSTROPHE => '\'',
            self::FORWARD_SLASH => '/',
        ];
    }
}
