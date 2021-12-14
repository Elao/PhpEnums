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

/**
 * @final
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
class EnumCase
{
    public function __construct(public readonly ?string $label = null)
    {
    }
}
