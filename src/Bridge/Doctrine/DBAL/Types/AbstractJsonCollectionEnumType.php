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
use Doctrine\DBAL\Types\JsonType;
use Elao\Enum\EnumInterface;

abstract class AbstractJsonCollectionEnumType extends JsonType
{
    /**
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (\is_array($value)) {
            $value = array_unique(array_values(array_map(static function (EnumInterface $enum) {
                return $enum->getValue();
            }, $value)));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $values = parent::convertToPHPValue($value, $platform);

        if (\is_array($values)) {
            $values = array_map([$this->getEnumClass(), 'get'], array_unique(array_values($values)));
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    abstract protected function getEnumClass(): string;
}
