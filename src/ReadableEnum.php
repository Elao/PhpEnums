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

abstract class ReadableEnum extends Enum implements ReadableEnumInterface
{
    /**
     * {@inheritdoc}
     */
    public function getReadable(): string
    {
        return static::readableFor($this->getValue());
    }

    /**
     * Converts to the human representation of the current value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getReadable();
    }

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
}
