<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
class EnumCase
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(public readonly ?string $label = null, public readonly array $extras = [])
    {
    }
}
