<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\ODM\Types;

use Elao\Enum\Bridge\Doctrine\Common\AbstractTypesDumper;
use Elao\Enum\Exception\LogicException;

/**
 * @internal
 */
class TypesDumper extends AbstractTypesDumper
{
    public const TYPE_SINGLE = 'single';
    public const TYPE_COLLECTION = 'collection';
    public const TYPES = [
        self::TYPE_SINGLE,
        self::TYPE_COLLECTION,
    ];

    protected function getTypeCode(
        string $classname,
        string $enumClass,
        string $type,
        string $name,
        \BackedEnum|int|string|null $defaultOnNull = null
    ): string {
        $code = <<<PHP
                        protected function getEnumClass(): string
                        {
                            return \\{$enumClass}::class;
                        }
            PHP;

        $baseClass = match ($type) {
            self::TYPE_SINGLE => AbstractEnumType::class,
            self::TYPE_COLLECTION => AbstractCollectionEnumType::class,
            default => throw new LogicException(sprintf('Unexpected type "%s"', $type)),
        };

        return <<<PHP

            if (!\class_exists($classname::class)) {
                class $classname extends \\{$baseClass}
                {
        $code
                }
            }

        PHP;
    }

    protected static function getSuffixes(): array
    {
        return [
            self::TYPE_SINGLE => 'Type',
            self::TYPE_COLLECTION => 'CollectionType',
        ];
    }

    protected static function getMarker(): string
    {
        return 'ELAO_ENUM_DT_ODM';
    }
}
