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

abstract class ReadableEnum extends Enum implements ReadableEnumInterface
{
    /**
     * {@inheritdoc}
     */
    public static function readableFor($value): string
    {
        if (!static::accepts($value)) {
            throw new InvalidValueException($value, static::class);
        }
        $humanRepresentations = static::readables();

        return $humanRepresentations[$value];
    }

    /**
     * {@inheritdoc}
     */
    public static function readablesFor(array $values): array
    {
        return array_map('self::readableFor', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function getReadable(): string
    {
        return static::readableFor($this->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getReadable();
    }
}
