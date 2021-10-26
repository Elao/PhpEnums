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
use Doctrine\ODM\MongoDB\Types\CollectionType;
use Elao\Enum\EnumInterface;

/**
 * @template TEnum of EnumInterface
 */
abstract class AbstractCollectionEnumType extends CollectionType
{
    use ClosureToPHP;

    /**
     * @return mixed
     */
    public function convertToDatabaseValue($value)
    {
        if (\is_array($value)) {
            return array_unique(array_values(array_map(static function ($value) {
                return $value instanceof EnumInterface ? $value->getValue() : $value;
            }, $value)));
        }

        return parent::convertToDatabaseValue($value);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-return array<TEnum>|null
     *
     * @return mixed
     */
    public function convertToPHPValue($value)
    {
        $values = parent::convertToPHPValue($value);

        if (\is_array($values)) {
            $enumClass = $this->getEnumClass();
            $values = array_map([$enumClass, 'get'], array_map(static function ($v) use ($enumClass) {
                // Attempt to cast to integer value if the enum class accepts it:
                return $enumClass::accepts((int) $v) ? (int) $v : $v;
            }, array_unique(array_values($values))));
        }

        return $values;
    }

    /**
     * @return class-string<TEnum>
     */
    abstract protected function getEnumClass(): string;
}
