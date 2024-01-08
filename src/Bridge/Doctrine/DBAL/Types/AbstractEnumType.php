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
     * @param \BackedEnum|int|string|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string|int|null
    {
        if ($value !== null && !is_a($value, $this->getEnumClass())) {
            $throwException = true;
            if ($this->checkIfValueMatchesBackedEnumType($value)) {
                $value = $this->getEnumClass()::tryFrom($this->cast($value));
                if ($value !== null) {
                    $throwException = false;
                }
            }

            if ($throwException) {
                throw new InvalidArgumentException(sprintf(
                    'Expected an instance of a %s. %s given.',
                    $this->getEnumClass(),
                    get_debug_type($value),
                ));
            }
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
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?\BackedEnum
    {
        if (null === $value) {
            return $this->onNullFromDatabase();
        }

        return $this->getEnumClass()::from($this->cast($value));
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($this->isIntBackedEnum()) {
            return $platform->getIntegerTypeDeclarationSQL($column);
        }

        if (empty($column['length'])) {
            $column['length'] = 255;
        }

        return method_exists($platform, 'getStringTypeDeclarationSQL') ?
            $platform->getStringTypeDeclarationSQL($column) :
            $platform->getVarcharTypeDeclarationSQL($column);
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
     */
    private function cast(int|string $value): int|string
    {
        return $this->isIntBackedEnum() ? (int) $value : (string) $value;
    }

    private function checkIfValueMatchesBackedEnumType(mixed $value): bool
    {
        return ($this->isIntBackedEnum() && \is_int($value)) || (!$this->isIntBackedEnum() && \is_string($value));
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
