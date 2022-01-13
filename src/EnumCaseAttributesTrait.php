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

use Elao\Enum\Attribute\EnumCase;

trait EnumCaseAttributesTrait
{
    private function getEnumCaseAttribute(): ?EnumCase
    {
        return static::enumCaseAttributes()[$this] ?? null;
    }

    /**
     * @return \SplObjectStorage<\UnitEnum,EnumCase>
     */
    private static function enumCaseAttributes(): \SplObjectStorage
    {
        static $attributes;

        if (!isset($attributes)) {
            $attributes = new \SplObjectStorage();

            foreach ((new \ReflectionEnum(static::class))->getCases() as $rCase) {
                if (null === $rAttr = $rCase->getAttributes(EnumCase::class)[0] ?? null) {
                    continue;
                }

                /** @var EnumCase $attr */
                $attr = $rAttr->newInstance();

                $attributes[$rCase->getValue()] = $attr;
            }
        }

        return $attributes;
    }
}
