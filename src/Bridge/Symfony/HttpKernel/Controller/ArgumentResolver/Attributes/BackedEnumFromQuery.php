<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes;

use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class BackedEnumFromQuery
{
    public function __construct()
    {
        trigger_deprecation(
            'elao/enum',
            '2.6',
            'The "%s" attribute is deprecated, use "%s" instead. It will be removed in 3.0.',
            self::class,
            MapQueryParameter::class,
        );
    }
}
