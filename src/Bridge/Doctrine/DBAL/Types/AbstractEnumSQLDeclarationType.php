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

/**
 * Base class for string enumerations with an `ENUM(...values)` column definition
 *
 * @template T of EnumInterface
 * @template-extends AbstractEnumType<T>
 */
abstract class AbstractEnumSQLDeclarationType extends AbstractEnumType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $values = implode(', ', array_map(static function (string $value): string {
            return "'$value'";
        }, ($this->getEnumClass())::values()));

        return "ENUM($values)";
    }
}
