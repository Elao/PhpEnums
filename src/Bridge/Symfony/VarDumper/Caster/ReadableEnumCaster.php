<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\VarDumper\Caster;

use Elao\Enum\ReadableEnumInterface;
use Symfony\Component\VarDumper\Caster\Caster;

final class ReadableEnumCaster
{
    public static function cast(ReadableEnumInterface $enum, $array)
    {
        return $array + [
            // Append the readable value;
            Caster::PREFIX_VIRTUAL . 'readable' => $enum->getReadable(),
        ];
    }
}
