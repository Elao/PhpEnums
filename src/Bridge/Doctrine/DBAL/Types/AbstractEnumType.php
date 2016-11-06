<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Elao\Enum\EnumInterface;

abstract class AbstractEnumType extends Type
{
    /**
     * The enum FQCN for which we should make the DBAL conversion.
     *
     * @return string
     */
    abstract protected function getEnumClass(): string;

    /**
     * What should be returned on null value from the database.
     *
     * @return mixed
     */
    protected function onNullFromDatabase()
    {
        return null;
    }

    /**
     * What should be returned on null value from PHP.
     *
     * @return mixed
     */
    protected function onNullFromPhp()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param EnumInterface $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $this->onNullFromPhp();
        }

        return $value->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $this->onNullFromDatabase();
        }

        /** @var string|EnumInterface $class */
        $class = $this->getEnumClass();

        return $class::create($this->cast($value));
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLength(AbstractPlatform $platform)
    {
        return $platform->getVarcharDefaultLength();
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * Cast the value from database to proper enumeration internal type.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast($value)
    {
        return (string) $value;
    }
}

class GenderEnumType extends AbstractEnumType
{
    const NAME = 'gender';

    protected function getEnumClass(): string
    {
        return Gender::class;
    }

    public function getName()
    {
        return static::NAME;
    }
}
