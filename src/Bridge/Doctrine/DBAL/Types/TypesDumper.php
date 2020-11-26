<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Exception\LogicException;

/**
 * @internal
 */
class TypesDumper
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

    public const TYPES_SUFFIXES = [
        self::TYPE_INT => 'Type',
        self::TYPE_STRING => 'Type',
        self::TYPE_ENUM => 'Type',
        self::TYPE_JSON_COLLECTION => 'JsonCollectionType',
        self::TYPE_CSV_COLLECTION => 'CsvCollectionType',
    ];

    public const MARKER = 'ELAO_ENUM_DT';

    public function dumpToFile(string $file, array $types)
    {
        file_put_contents($file, $this->dump($types));
    }

    private function dump(array $types): string
    {
        $namespaces = [];
        foreach ($types as [$enumClass, $type, $name]) {
            $fqcn = self::getTypeClassname($enumClass, $type);
            $classname = basename(str_replace('\\', '/', $fqcn));
            $ns = substr($fqcn, 0, -\strlen($classname) - 1);

            if (!isset($namespaces[$ns])) {
                $namespaces[$ns] = '';
            }

            $namespaces[$ns] .= $this->getTypeCode($classname, $enumClass, $type, $name);
        }

        $code = "<?php\n";
        foreach ($namespaces as $namespace => $typeCode) {
            $code .= <<<PHP

namespace $namespace {
$typeCode
}

PHP;
        }

        return $code;
    }

    private function getTypeCode(string $classname, string $enumClass, string $type, string $name): string
    {
        switch ($type) {
            case self::TYPE_INT:
                $baseClass = AbstractIntegerEnumType::class;
                break;
            case self::TYPE_STRING:
                $baseClass = AbstractEnumType::class;
                break;
            case self::TYPE_ENUM:
                $baseClass = AbstractEnumSQLDeclarationType::class;
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
            public const NAME = '$name';

            protected function getEnumClass(): string
            {
                return \\{$enumClass}::class;
            }

            public function getName(): string
            {
                return static::NAME;
            }
        }
    }

PHP;
    }

    public static function getTypeClassname(string $class, string $type): string
    {
        $suffix = self::TYPES_SUFFIXES[$type];

        return self::MARKER . "\\{$class}{$suffix}";
    }
}
