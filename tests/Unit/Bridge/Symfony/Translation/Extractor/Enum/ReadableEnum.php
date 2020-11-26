<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Translation\Extractor\Enum;

use Elao\Enum\ReadableEnum as BaseReadableEnum;

final class ReadableEnum extends BaseReadableEnum
{
    public const READABLE_ENUM = 'readable_enum';
    public const READABLE_ENUM_EMPTY = 'readable_enum_empty';

    /**
     * {@inheritdoc}
     */
    public static function values(): array
    {
        return [
            static::READABLE_ENUM,
            static::READABLE_ENUM_EMPTY,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function readables(): array
    {
        return [
            self::READABLE_ENUM => 'trans_readable_enum',
            self::READABLE_ENUM_EMPTY => '',
        ];
    }
}
