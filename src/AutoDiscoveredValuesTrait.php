<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\LogicException;

/**
 * Auto-discover enumerated values from public constants.
 */
trait AutoDiscoveredValuesTrait
{
    /** @internal */
    private static $guessedValues = [];

    /** @internal */
    private static $guessedReadables = [];

    /**
     * @see EnumInterface::values()
     *
     * @return int[]|string[]
     */
    public static function values(): array
    {
        return self::autodiscoveredValues();
    }

    /**
     * @see ChoiceEnumTrait::choices()
     */
    protected static function choices(): array
    {
        if (!\in_array(ChoiceEnumTrait::class, class_uses(self::class, false), true)) {
            throw new LogicException(sprintf(
                'Method "%s" is only meant to be used when using the "%s" trait which is not used in "%s"',
                __METHOD__,
                ChoiceEnumTrait::class,
                static::class
            ));
        }

        return self::autodiscoveredReadables();
    }

    /**
     * @internal
     */
    private static function autodiscoveredValues(): array
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
                    return \is_int($v) && 0 === ($v & $v - 1) && $v > 0;
                });
            } else {
                $values = array_filter($values, function ($v) {
                    return \is_int($v) || \is_string($v);
                });
            }

            self::$guessedValues[$enumType] = array_values($values);
        }

        return self::$guessedValues[$enumType];
    }

    /**
     * @internal
     */
    private static function autodiscoveredReadables(): array
    {
        $enumType = static::class;

        if (!isset(self::$guessedReadables[$enumType])) {
            $constants = (new \ReflectionClass($enumType))->getConstants();
            foreach (self::autodiscoveredValues() as $value) {
                $constantName = array_search($value, $constants, true);
                self::$guessedReadables[$enumType][$value] = ucfirst(strtolower(str_replace('_', ' ', $constantName)));
            }
        }

        return self::$guessedReadables[$enumType];
    }
}
