<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

interface ExtrasInterface extends EnumCaseInterface
{
    /**
     * @throws \InvalidArgumentException if $throwOnMissingExtra is set to true
     */
    public function getExtra(string $key, bool $throwOnMissingExtra = false): mixed;

    /**
     * @return iterable<static, mixed>
     */
    public static function extras(string $key, bool $throwOnMissingExtra = false): iterable;
}
