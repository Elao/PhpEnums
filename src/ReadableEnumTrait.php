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

/**
 * @see ReadableEnumInterface which this trait implements
 */
trait ReadableEnumTrait
{
    use EnumTrait;

    /**
     * {@inheritdoc}
     */
    public static function readableFor($value): string
    {
        if (!static::accepts($value)) {
            throw new InvalidValueException($value, static::class);
        }

        return static::readables()[$value];
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
