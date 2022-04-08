<?php

declare(strict_types=1);

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

/**
 * @template TEnum of \BackedEnum
 */
abstract class AbstractEnumType extends Type
{
    use ClosureToPHP;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value): int|string|null
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return TEnum
     */
    public function convertToPHPValue($value): ?\BackedEnum
    {
        if (null === $value) {
            return null;
        }

        $class = $this->getEnumClass();

        return $class::from($value);
    }

    /**
     * @return class-string<TEnum>
     */
    abstract protected function getEnumClass(): string;
}
