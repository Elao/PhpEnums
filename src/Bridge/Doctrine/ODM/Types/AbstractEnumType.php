<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\ODM\Types;

use Doctrine\ODM\MongoDB\Types\ClosureToPHP;
use Doctrine\ODM\MongoDB\Types\Type;
use Elao\Enum\EnumInterface;

/**
 * @template TEnum of EnumInterface
 */
abstract class AbstractEnumType extends Type
{
    use ClosureToPHP;

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value)
    {
        if (null === $value) {
            return null;
        }

        $class = static::getEnumClass();

        if ($value instanceof $class) {
            return $value->getValue();
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return TEnum
     */
    public function convertToPHPValue($value): ?EnumInterface
    {
        if (null === $value) {
            return null;
        }

        $class = $this->getEnumClass();

        return $class::get($value);
    }

    /**
     * @return class-string<TEnum>
     */
    abstract protected function getEnumClass(): string;
}
