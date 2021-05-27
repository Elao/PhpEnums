<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Doctrine\Common;

/**
 * @internal
 */
abstract class AbstractTypesDumper
{
    public function dumpToFile(string $file, array $types)
    {
        file_put_contents($file, $this->dump($types));
    }

    public static function getTypeClassname(string $class, string $type): string
    {
        return sprintf('%s\\%s%s', static::getMarker(), $class, static::getSuffixes()[$type]);
    }

    protected function dump(array $types): string
    {
        array_walk($types, static function (&$type) {
            $type = array_pad($type, 4, null);
        });

        $namespaces = [];
        foreach ($types as [$enumClass, $type, $name, $default]) {
            $fqcn = self::getTypeClassname($enumClass, $type);
            $classname = basename(str_replace('\\', '/', $fqcn));
            $ns = substr($fqcn, 0, -\strlen($classname) - 1);

            if (!isset($namespaces[$ns])) {
                $namespaces[$ns] = '';
            }

            $namespaces[$ns] .= $this->getTypeCode($classname, $enumClass, $type, $name, $default);
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

    abstract protected function getTypeCode(
        string $classname,
        string $enumClass,
        string $type,
        string $name,
        $defaultOnNull = null
    ): string;

    abstract protected static function getSuffixes(): array;

    abstract protected static function getMarker(): string;
}
