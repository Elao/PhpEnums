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

use BackedEnum;
use Doctrine\ODM\MongoDB\Types\ClosureToPHP;
use Doctrine\ODM\MongoDB\Types\CollectionType;

/**
 * @template TEnum of \BackedEnum
 */
abstract class AbstractCollectionEnumType extends CollectionType
{
    use ClosureToPHP;

    public function convertToDatabaseValue($value): ?array
    {
        if (\is_array($value)) {
            return array_unique(array_values(array_map(static function ($value) {
                return $value instanceof BackedEnum ? $value->value : $value;
            }, $value)));
        }

        return parent::convertToDatabaseValue($value);
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-return array<TEnum>|null
     */
    public function convertToPHPValue($value): ?array
    {
        $values = parent::convertToPHPValue($value);

        if (\is_array($values)) {
            $enumClass = $this->getEnumClass();
            $values = array_map([$enumClass, 'from'], array_unique(array_values($values)));
        }

        return $values;
    }

    /**
     * @return class-string<TEnum>
     */
    abstract protected function getEnumClass(): string;
}
