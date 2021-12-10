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

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Elao\Enum\Exception\InvalidArgumentException;

abstract class AbstractEnumType extends Type
{
    private bool $isIntBackedEnum;

    /**
     * The enum FQCN for which we should make the DBAL conversion.
     *
     * @psalm-return class-string<\BackedEnum>
     */
    abstract protected function getEnumClass(): string;

    /**
     * What should be returned on null value from the database.
     */
    protected function onNullFromDatabase(): ?\BackedEnum
    {
        return null;
    }

    /**
     * What should be returned on null value from PHP.
     */
    protected function onNullFromPhp(): int|string|null
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param \BackedEnum|null $value
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value !== null && !$value instanceof \BackedEnum) {
            throw new InvalidArgumentException(sprintf(
                'Expected an instance of a %s. %s given.',
                \BackedENum::class,
                get_debug_type($value),
            ));
        }

        if (null === $value) {
            return $this->onNullFromPhp();
        }

        return $value->value;
    }

    /**
     * {@inheritdoc}
     *
     * @param int|string|null $value The value to convert.
     *
     * @return \BackedEnum|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $this->onNullFromDatabase();
        }

        return $this->getEnumClass()::from($this->cast($value));
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $this->isIntBackedEnum()
            ? $platform->getIntegerTypeDeclarationSQL($fieldDeclaration)
            : $platform->getVarcharTypeDeclarationSQL($fieldDeclaration)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType(): int
    {
        return $this->isIntBackedEnum() ? ParameterType::INTEGER : ParameterType::STRING;
    }

    /**
     * Cast the value from database to proper enumeration internal type.
     *
     * @param int|string $value
     */
    private function cast($value): int|string
    {
        return $this->isIntBackedEnum() ? (int) $value : (string) $value;
    }

    private function isIntBackedEnum(): bool
    {
        if (!isset($this->isIntBackedEnum)) {
            $r = new \ReflectionEnum($this->getEnumClass());

            $this->isIntBackedEnum = 'int' === (string) $r->getBackingType();
        }

        return $this->isIntBackedEnum;
    }

    public function getName(): string
    {
        return $this->getEnumClass();
    }
}
