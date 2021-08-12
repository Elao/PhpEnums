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
use Stringable;

/**
 * @template T of int|string
 * @extends EnumInterface<T>
 */
interface ReadableEnumInterface extends EnumInterface, Stringable
{
    /**
     * Gets an array of the human representations indexed by possible values.
     *
     * @return string[] labels indexed by enumerated value
     * @psalm-return array<T, string>
     */
    public static function readables(): array;

    /**
     * Gets the human representation for a given value.
     *
     * @param int|string $value The value of a particular enumerated constant
     * @psalm-param T $value
     *
     * @throws InvalidValueException When $value is not acceptable for this enumeration type
     *
     * @return string The human representation for a given value
     */
    public static function readableFor($value): string;

    /**
     * Gets the human representation of the value.
     */
    public function getReadable(): string;

    /**
     * Converts to the human representation of the current value.
     *
     * @return string
     */
    public function __toString();
}
