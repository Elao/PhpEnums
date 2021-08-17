<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\InvalidValueException;

/**
 * @template T of int|string
 */
interface EnumInterface
{
    /**
     * @param int|string $value The value of a particular enumerated constant
     * @psalm-param T $value
     *
     * @throws InvalidValueException When $value is not acceptable for this enumeration type
     *
     * @return static The enum instance for given value
     */
    public static function get($value): self;

    /**
     * Returns any possible value for the enumeration.
     *
     * @return int[]|string[]
     * @psalm-return T[]
     */
    public static function values(): array;

    /**
     * @param int|string $value
     * @psalm-param T $value
     *
     * @return bool True if the value is acceptable for this enumeration
     */
    public static function accepts($value): bool;

    /**
     * Returns the list of all possible enum instances.
     *
     * @return static[]
     */
    public static function instances(): array;

    /**
     * Gets the raw value.
     *
     * @return int|string
     * @psalm-return T
     */
    public function getValue();

    /**
     * Determines whether two enumerations instances should be considered the same.
     *
     * @param EnumInterface<int|string> $enum An enum object to compare with this instance
     * @psalm-param EnumInterface<T> $enum
     */
    public function equals(EnumInterface $enum): bool;

    /**
     * Determines if the enumeration instance value is equal to the given value.
     *
     * @param int|string $value
     * @psalm-param T $value
     */
    public function is($value): bool;
}
