<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\NameException;

interface ReadableEnumInterface extends \UnitEnum
{
    /**
     * Gets an array of the human representations indexed by enum cases names.
     *
     * @return string[] labels indexed by enum cases names
     * @psalm-return array<string, string>
     */
    public static function readables(): array;

    /**
     * TODO: only for backed enums
     *
     * Gets the human representation for a given value.
     *
     * @throws \ValueError When $value is not acceptable for this enum
     *
     * @return string The human representation for a given value
     */
    public static function readableForValue(string|int $value): string;

    /**
     * Gets the human representation for a given name.
     *
     * @throws NameException When $name is not acceptable for this enum
     *
     * @return string The human representation for a given value
     */
    public static function readableForName(string $value): string;

    /**
     * Gets the human representation of the value.
     */
    public function getReadable(): string;
}
