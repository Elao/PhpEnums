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
 * @template T of int|string
 *
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
     * @psalm-return T[]
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
        $discoveredClasses = array_reverse(static::getDiscoveredClasses());

        if (!isset(self::$guessedValues[$enumType])) {
            self::$guessedValues[$enumType] = [];
            foreach ($discoveredClasses as $discoveredClass) {
                $r = new \ReflectionClass($discoveredClass);
                $values = $r->getConstants();

                $values = array_filter($values, static function (string $k) use ($r) {
                    return $r->getReflectionConstant($k)->isPublic();
                }, ARRAY_FILTER_USE_KEY);

                if (is_a($enumType, FlaggedEnum::class, true)) {
                    $values = array_filter($values, static function ($v) {
                        return \is_int($v) && 0 === ($v & $v - 1) && $v > 0;
                    });
                } else {
                    $values = array_filter($values, static function ($v) {
                        return \is_int($v) || \is_string($v);
                    });
                }

                self::$guessedValues[$enumType] = array_merge(self::$guessedValues[$enumType], array_values($values));
            }

            self::$guessedValues[$enumType] = array_values(array_unique(self::$guessedValues[$enumType]));
        }

        return self::$guessedValues[$enumType];
    }

    /**
     * @internal
     */
    private static function autodiscoveredReadables(): array
    {
        $enumType = static::class;
        $discoveredClasses = array_reverse(static::getDiscoveredClasses());
        $values = self::autodiscoveredValues();

        if (!isset(self::$guessedReadables[$enumType])) {
            $constants = [];
            foreach ($discoveredClasses as $discoveredClass) {
                $constants = array_replace($constants, (new \ReflectionClass($discoveredClass))->getConstants());
            }

            foreach ($values as $value) {
                $constantName = array_search($value, $constants, true);
                self::$guessedReadables[$enumType][$value] = ucfirst(strtolower(str_replace('_', ' ', $constantName)));
            }
        }

        return self::$guessedReadables[$enumType];
    }

    /**
     * Override this method in order to discover values from public int/string constants in other classes.
     * E.g: `return [static::class, DumbEnum::class];` to discover values from DumbEnum as well.
     *
     * @return string[]
     * @psalm-return class-string[]
     */
    protected static function getDiscoveredClasses(): array
    {
        return [static::class];
    }
}
