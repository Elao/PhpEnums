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

interface EnumInterface
{
    /**
     * @param int|string $value The value of a particular enumerated constant
     *
     * @throws InvalidValueException When $value is not acceptable for this enumeration type
     *
     * @return EnumInterface The enum instance for given value
     */
    public static function get($value): self;

    /**
     * Returns any possible value for the enumeration.
     *
     * @return int[]|string[]
     */
    public static function values(): array;

    /**
     * @param int|string $value
     *
     * @return bool True if the value is acceptable for this enumeration
     */
    public static function accepts($value): bool;

    /**
     * Returns the list of all possible enum instances.
     *
     * @return EnumInterface[]
     */
    public static function instances(): array;

    /**
     * Gets the raw value.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Determines whether two enumerations instances should be considered the same.
     *
     * @param EnumInterface $enum An enum object to compare with this instance
     */
    public function equals(EnumInterface $enum): bool;

    /**
     * Determines if the enumeration instance value is equal to the given value.
     *
     * @param int|string $value
     */
    public function is($value): bool;
}
