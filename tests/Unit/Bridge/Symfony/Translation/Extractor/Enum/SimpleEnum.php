<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Translation\Extractor\Enum;

use Elao\Enum\Enum;

final class SimpleEnum extends Enum
{
    public const SIMPLE = 'simple';

    /**
     * {@inheritdoc}
     */
    public static function values(): array
    {
        return [
            static::SIMPLE,
        ];
    }
}
