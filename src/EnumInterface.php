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
     * Gets an array of the possible values.
     *
     * @return array
     */
    public static function getPossibleValues(): array;

    /**
     * Tells is this value is acceptable.
     *
     * @param mixed $value
     *
     * @return bool True if $value is acceptable for this enumeration type; otherwise false
     */
    public static function isAcceptableValue($value): bool;

    /**
     * Gets the raw value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Determines whether enums are equals.
     *
     * @param EnumInterface $enum An enum object to compare with this instance
     *
     * @return bool True if $enum is an enum with the same type and value as this instance; otherwise, false
     */
    public function equals(EnumInterface $enum): bool;

    /**
     * Determine if the enum value is equal to the given value.
     *
     * @param $value
     *
     * @return bool
     */
    public function is($value): bool;

    /**
     * Returns the list of all possible enum instances.
     *
     * @return EnumInterface[]
     */
    public static function getPossibleInstances(): array;
}
