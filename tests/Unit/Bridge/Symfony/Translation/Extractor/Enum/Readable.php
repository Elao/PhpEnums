<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Translation\Extractor\Enum;

use Elao\Enum\ReadableEnum;

final class Readable extends ReadableEnum
{
    public const READABLE = 'readable';

    /**
     * {@inheritdoc}
     */
    public static function values(): array
    {
        return [
            static::READABLE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function readables(): array
    {
        return [
            self::READABLE => 'trans_readable',
        ];
    }
}
