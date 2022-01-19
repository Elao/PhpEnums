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
 * @template T of EnumInterface
 * @template-extends AbstractEnumType<T>
 */
abstract class AbstractIntegerEnumType extends AbstractEnumType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType(): int
    {
        return \PDO::PARAM_INT;
    }

    /**
     * {@inheritdoc}
     */
    protected function cast($value)
    {
        return (int) $value;
    }
}
