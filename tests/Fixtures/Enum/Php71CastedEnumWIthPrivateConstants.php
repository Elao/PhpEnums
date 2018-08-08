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

class Php71CastedEnumWIthPrivateConstants extends Enum
{
    public const FOO = 'foo';
    private const PRIVATE_FOO = 'foo';

    public static function values(): array
    {
        return [self::FOO];
    }
}
