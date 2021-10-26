<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SimpleArrayType;
use Elao\Enum\EnumInterface;

abstract class AbstractCsvCollectionEnumType extends SimpleArrayType
{
    /**
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (\is_array($value)) {
            return implode(',', array_unique(array_values(array_map(static function (EnumInterface $enum) {
                return $enum->getValue();
            }, $value))));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value === '') {
            // An empty csv means an empty array:
            return [];
        }

        $values = parent::convertToPHPValue($value, $platform);

        if (\is_array($values)) {
            $enumClass = $this->getEnumClass();
            $values = array_map([$enumClass, 'get'], array_map(static function ($v) use ($enumClass) {
                // Attempt to cast to integer value if the enum class accepts it:
                return $enumClass::accepts((int) $v) ? (int) $v : $v;
            }, array_unique(array_values($values))));
        }

        return $values;
    }

    abstract protected function getEnumClass(): string;
}
