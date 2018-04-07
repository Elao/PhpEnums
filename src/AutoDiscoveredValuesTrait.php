<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
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
            $r = new \ReflectionClass($enumType);
            $values = $r->getConstants();

            if (PHP_VERSION_ID >= 70100) {
                $values = array_filter($values, function (string $k) use ($r) {
                    return $r->getReflectionConstant($k)->isPublic();
                }, ARRAY_FILTER_USE_KEY);
            }

            if (is_a($enumType, FlaggedEnum::class, true)) {
                $values = array_filter($values, function ($v) {
                    return is_int($v) && 0 === ($v & $v - 1) && $v > 0;
                });
            }

            self::$guessedValues[$enumType] = array_combine($values, $values);
        }

        return self::$guessedValues[$enumType];
    }
}
