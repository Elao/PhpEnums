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

interface ReadableEnumInterface extends EnumInterface
{
    /**
     * Gets an array of the human representations indexed by possible values.
     *
     * @return string[] labels indexed by enumerated value
     */
    public static function readables(): array;

    /**
     * Gets the human representation for a given value.
     *
     * @param mixed $value The value of a particular enumerated constant
     *
     * @throws InvalidValueException When $value is not acceptable for this enumeration type
     *
     * @return string The human representation for a given value
     */
    public static function readableFor($value): string;

    /**
     * Gets the human representations for given values.
     *
     * @param array $values
     *
     * @throws InvalidValueException When $value is not acceptable for this enumeration type
     *
     * @return array
     */
    public static function readablesFor(array $values): array;

    /**
     * Gets the human representation of the value.
     *
     * @return string
     */
    public function getReadable(): string;

    /**
     * Converts to the human representation of the current value.
     *
     * @return string
     */
    public function __toString();
}
