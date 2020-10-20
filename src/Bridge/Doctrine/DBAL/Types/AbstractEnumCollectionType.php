<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Elao\Enum\Enum;

abstract class AbstractEnumCollectionType extends JsonType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (\is_array($value)) {
            $value = array_map(function (Enum $enum) { return $enum->getValue(); }, $value);
        }

        return parent::convertToDatabaseValue(array_values($value), $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $values = parent::convertToPHPValue($value, $platform);

        if (\is_array($values)) {
            $values = array_map(function ($value) {
                return $this->getEnumClass()::get($value);
            }, $values);
        }

        return $values;
    }

    abstract protected function getEnumClass(): string;
}
