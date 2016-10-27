<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\InvalidEnumArgumentException;

abstract class ReadableEnum extends Enum implements ReadableEnumInterface
{
    /**
     * {@inheritdoc}
     */
    public function getReadable(): string
    {
        return static::getReadableFor($this->getValue());
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
    public static function getReadableFor($value): string
    {
        if (!static::isAcceptableValue($value)) {
            throw new InvalidEnumArgumentException($value, static::class);
        }
        $humanRepresentations = static::getReadables();

        return $humanRepresentations[$value];
    }
}
