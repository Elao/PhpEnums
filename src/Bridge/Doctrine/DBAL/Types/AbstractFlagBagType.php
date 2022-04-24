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
use Elao\Enum\Exception\LogicException;
use Elao\Enum\FlagBag;

abstract class AbstractFlagBagType extends Type
{
    /**
     * The enum FQCN for which we should make the DBAL conversion.
     *
     * @psalm-return class-string<\BackedEnum>
     */
    abstract protected function getEnumClass(): string;

    /**
     * What should be returned on null value from the database.
     */
    protected function onNullFromDatabase(): ?FlagBag
    {
        return null;
    }

    /**
     * What should be returned on null value from PHP.
     */
    protected function onNullFromPhp(): int|null
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param FlagBag|null $value
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        if ($value !== null && !$value instanceof FlagBag) {
            throw new InvalidArgumentException(sprintf(
                'Expected an instance of a %s. %s given.',
                FlagBag::class,
                get_debug_type($value),
            ));
        }

        if (null === $value) {
            return $this->onNullFromPhp();
        }

        return $value->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @param int|null $value The value to convert.
     *
     * @return FlagBag|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $this->onNullFromDatabase();
        }

        if (!\is_int($value)) {
            throw new InvalidArgumentException(sprintf(
                'Expected int. %s given.',
                get_debug_type($value),
            ));
        }

        return new FlagBag($this->getEnumClass(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $enumBackingType = (new \ReflectionEnum($this->getEnumClass()))->getBackingType();

        if ('int' !== (string) $enumBackingType) {
            throw new LogicException(sprintf('Expecting int backed enum, %s given', $enumBackingType));
        }

        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
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
        return ParameterType::INTEGER;
    }

    public function getName(): string
    {
        return $this->getEnumClass();
    }
}
