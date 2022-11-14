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
 * Autoconfigure a readable enum cases' labels, using the name or value + allow to configure a prefix and/or suffix for the key.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ReadableEnum
{
    public function __construct(
        public readonly ?string $prefix = null,
        public readonly ?string $suffix = null,
        public readonly bool $useValueAsDefault = false
    ) {
    }
}
