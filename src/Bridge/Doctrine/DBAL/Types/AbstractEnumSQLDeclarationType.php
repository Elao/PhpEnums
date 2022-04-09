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
 */
abstract class AbstractEnumSQLDeclarationType extends AbstractEnumType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $values = array_map(
            function ($val) {
                return "'" . $val->value . "'";
            },
            ($this->getEnumClass())::cases()
        );

        return "ENUM(" . implode(", ", $values) . ")";
    }
}
