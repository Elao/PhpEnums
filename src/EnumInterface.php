<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\InvalidValueException;

interface EnumInterface
{
    /**
     * Instantiates a new enumeration.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @throws InvalidValueException When $value is not acceptable for this enumeration type
     *
     * @return EnumInterface A new instance of an enum
     */
    public static function create($value): EnumInterface;

    /**
     * Returns any possible value for the enumeration.
     *
     * @return array
     */
    public static function values(): array;

    /**
     * @param mixed $value
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
     * @return mixed
     */
    public function getValue();

    /**
     * Determines whether two enumerations instances should be considered the same.
     *
     * @param EnumInterface $enum An enum object to compare with this instance
     *
     * @return bool
     */
    public function equals(EnumInterface $enum): bool;

    /**
     * Determines if the enumeration instance value is equal to the given value.
     *
     * @param $value
     *
     * @return bool
     */
    public function is($value): bool;
}
