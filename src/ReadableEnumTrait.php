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

trait ReadableEnumTrait
{
    /**
     * {@inheritdoc}
     */
    public static function readableForValue(string|int $value): string
    {
        /** @var \UnitEnum $case */
        $case = static::from($value);

        return (static::readables())[$case->name];
    }

    /**
     * {@inheritdoc}
     */
    public static function readableForName(string $name): string
    {
        self::guardValidName($name);

        return (static::readables())[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getReadable(): string
    {
        return static::readableForValue($this->value);
    }

    private static function guardValidName(string $name): void
    {
        /** @var array<string,\UnitEnum>|null $map */
        static $map;

        if (null === $map) {
            $map = array_combine(array_map(fn (\UnitEnum $e) => $e->name, static::cases()), static::cases());
        }

        if (!isset($map[$name])) {
            throw new NameException($name, static::class);
        }
    }
}
