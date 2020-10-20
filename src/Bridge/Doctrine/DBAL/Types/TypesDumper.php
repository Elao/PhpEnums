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
    const MARKER = 'ELAO_ENUM_DT';

    public function dumpToFile(string $file, array $types)
    {
        file_put_contents($file, $this->dump($types));
    }

    private function dump(array $types): string
    {
        $namespaces = [];
        foreach ($types as [$enumClass, $type, $name]) {
            $fqcn = self::getTypeClassname($enumClass);
            $classname = basename(str_replace('\\', '/', $fqcn));
            $ns = substr($fqcn, 0, -\strlen($classname) - 1);

            if (!isset($namespaces[$ns])) {
                $namespaces[$ns] = '';
            }

            $namespaces[$ns] .= $this->getTypeCode($fqcn, $classname, $enumClass, $type, $name);
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

    private function getTypeCode(string $fqcn, string $classname, string $enumClass, string $type, string $name): string
    {
        switch ($type) {
            case 'int':
                $baseClass = AbstractIntegerEnumType::class;
                break;
            case 'string':
                $baseClass = AbstractEnumType::class;
                break;
            case 'enum':
                $baseClass = AbstractEnumSQLDeclarationType::class;
                break;
            case 'collection':
                $baseClass = AbstractEnumCollectionType::class;
                break;
            default:
                throw new LogicException(sprintf('Unexpected type "%s"', $type));
        }

        return <<<PHP

    if (!\class_exists(\\$fqcn::class)) {
        class $classname extends \\{$baseClass}
        {
            const NAME = '$name';

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

    public static function getTypeClassname(string $class): string
    {
        return self::MARKER . "\\{$class}Type";
    }
}
