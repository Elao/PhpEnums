<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

trait AutoDiscoveredValuesTrait
{
    /** @var array */
    private static $guessedValues = [];

    /**
     * @see EnumInterface::values()
     *
     * @return int[]|string[]
     */
    public static function values(): array
    {
        $enumType = static::class;

        if (!isset(self::$guessedValues[$enumType])) {
            $values = (new \ReflectionClass($enumType))->getConstants();

            if (is_a($enumType, FlaggedEnum::class, true)) {
                $values = array_filter($values, function ($v) {
                    return 0 === ($v & $v - 1) && $v > 0;
                });
            }

            self::$guessedValues[$enumType] = array_values($values);
        }

        return self::$guessedValues[$enumType];
    }
}
