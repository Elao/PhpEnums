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
use Elao\Enum\FlagBag;

if (enum_exists(ParameterType::class)) {
    /**
     * For doctrine/dbal 4
     *
     * @internal
     */
    trait DbalVersionFlagBagTypeTrait
    {
        /**
         * {@inheritdoc}
         */
        public function getBindingType(): ParameterType
        {
            return ParameterType::INTEGER;
        }
    }
} else {
    /**
     * For doctrine/dbal 3
     *
     * @internal
     */
    trait DbalVersionFlagBagTypeTrait
    {
        /**
         * {@inheritdoc}
         */
        public function getBindingType(): int
        {
            return ParameterType::INTEGER;
        }
    }
}

abstract class AbstractFlagBagType extends Type
{
    use DbalVersionFlagBagTypeTrait;

    /**
     * The enum FQCN for which we should make the DBAL conversion.
     *
     * @psalm-return class-string<\BackedEnum>
     */
    abstract protected function getEnumClass(): string;

    /**
     * What should be returned on null value from the database.
     *
     * @return FlagBag<\BackedEnum>|null
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
     * @param FlagBag<\BackedEnum>|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
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
     * @return FlagBag<\BackedEnum>|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?FlagBag
    {
        $value = parent::convertToPHPValue($value, $platform);

        if (null === $value) {
            return $this->onNullFromDatabase();
        }

        return new FlagBag($this->getEnumClass(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->getEnumClass();
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }
}
