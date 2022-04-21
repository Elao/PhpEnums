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

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Elao\Enum\Exception\LogicException;

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
        if (!$platform instanceof AbstractMySQLPlatform) {
            throw new LogicException('SQL ENUM type is not supported on the current platform');
        }

        $values = array_map(
            static fn ($val) => "'{$val->value}'",
            $this->getEnumClass()::cases()
        );

        return 'ENUM(' . implode(', ', $values) . ')';
    }
}
