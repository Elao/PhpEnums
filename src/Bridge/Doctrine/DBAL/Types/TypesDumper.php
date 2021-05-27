<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\Common\AbstractTypesDumper;
use Elao\Enum\Exception\LogicException;

/**
 * @internal
 */
class TypesDumper extends AbstractTypesDumper
{
    public const TYPE_INT = 'int';
    public const TYPE_STRING = 'string';
    public const TYPE_ENUM = 'enum';
    public const TYPE_JSON_COLLECTION = 'json_collection';
    public const TYPE_CSV_COLLECTION = 'csv_collection';
    public const TYPES = [
        self::TYPE_INT,
        self::TYPE_STRING,
        self::TYPE_ENUM,
        self::TYPE_JSON_COLLECTION,
        self::TYPE_CSV_COLLECTION,
    ];

    protected function getTypeCode(
        string $classname,
        string $enumClass,
        string $type,
        string $name,
        $defaultOnNull = null
    ): string {
        $code = <<<PHP
            public const NAME = '$name';

            protected function getEnumClass(): string
            {
                return \\{$enumClass}::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
PHP;
        switch ($type) {
            case self::TYPE_INT:
                $baseClass = AbstractIntegerEnumType::class;
                $this->appendDefaultOnNullMethods($defaultOnNull, $enumClass, $code);
                break;
            case self::TYPE_STRING:
                $baseClass = AbstractEnumType::class;
                $this->appendDefaultOnNullMethods($defaultOnNull, $enumClass, $code);
                break;
            case self::TYPE_ENUM:
                $baseClass = AbstractEnumSQLDeclarationType::class;
                $this->appendDefaultOnNullMethods($defaultOnNull, $enumClass, $code);
                break;
            case self::TYPE_JSON_COLLECTION:
                $baseClass = AbstractJsonCollectionEnumType::class;
                break;
            case self::TYPE_CSV_COLLECTION:
                $baseClass = AbstractCsvCollectionEnumType::class;
                break;
            default:
                throw new LogicException(sprintf('Unexpected type "%s"', $type));
        }

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
            self::TYPE_INT => 'Type',
            self::TYPE_STRING => 'Type',
            self::TYPE_ENUM => 'Type',
            self::TYPE_JSON_COLLECTION => 'JsonCollectionType',
            self::TYPE_CSV_COLLECTION => 'CsvCollectionType',
        ];
    }

    protected static function getMarker(): string
    {
        return 'ELAO_ENUM_DT_DBAL';
    }

    private function appendDefaultOnNullMethods($defaultOnNull, string $enumClass, string &$code): void
    {
        if ($defaultOnNull !== null) {
            $defaultOnNullCode = var_export($defaultOnNull, true);
            $code .= <<<PHP

            protected function onNullFromDatabase()
            {
                return \\{$enumClass}::get({$defaultOnNullCode});
            }

            protected function onNullFromPhp()
            {
                return {$defaultOnNullCode};
            }
PHP;
        }
    }
}
