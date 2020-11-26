<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Translation\Extractor\Enum\Ignore;

use Elao\Enum\ReadableEnum;

final class IgnoredEnum extends ReadableEnum
{
    public const IGNORED_ENUM = 'ignored_enum';

    /**
     * {@inheritdoc}
     */
    public static function values(): array
    {
        return [
            static::IGNORED_ENUM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function readables(): array
    {
        return [
            self::IGNORED_ENUM => 'trans_ignored_enum',
        ];
    }
}
