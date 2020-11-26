<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\SimpleChoiceEnum;

final class SimpleChoiceEnumFromDumEnum extends SimpleChoiceEnum
{
    public const BAZ = 'baz';

    protected static function getDiscoveredClasses(): array
    {
        return [self::class, DumbEnum::class];
    }
}
