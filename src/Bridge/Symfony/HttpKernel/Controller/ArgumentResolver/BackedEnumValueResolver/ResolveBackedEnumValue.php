<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver;

/**
 * Indicates from which request's components a controller argument should be resolved.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class ResolveBackedEnumValue
{
    public readonly array $from;

    public function __construct(
        /* @var ResolveFrom[]|ResolveFrom */
        array|ResolveFrom $from = [ResolveFrom::Attributes],
    ) {
        $this->from = $from instanceof ResolveFrom ? [$from] : $from;
    }
}
