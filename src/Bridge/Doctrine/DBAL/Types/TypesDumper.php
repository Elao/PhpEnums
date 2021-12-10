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

/**
 * @internal
 */
class TypesDumper
{
    /**
     * @return void
     */
    public function dumpToFile(string $file, array $types)
    {
        file_put_contents($file, $this->dump($types));
    }

    public static function getTypeClassname(string $class): string
    {
        return sprintf('%s\\%sType', static::getMarker(), $class);
    }

    private function dump(array $types): string
    {
        array_walk($types, static function (& $type) {
            $type = array_pad($type, 3, null);
        });

        $namespaces = [];
        foreach ($types as [$enumClass, $name, $default]) {
            $fqcn = self::getTypeClassname($enumClass);
            $classname = basename(str_replace('\\', '/', $fqcn));
            $ns = substr($fqcn, 0, -\strlen($classname) - 1);

            if (!isset($namespaces[$ns])) {
                $namespaces[$ns] = '';
            }

            $namespaces[$ns] .= $this->getTypeCode($classname, $enumClass, $name, $default);
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

    private function getTypeCode(
        string $classname,
        string $enumClass,
        string $name,
        \BackedEnum|int|string|null $defaultOnNull = null
    ): string {
        $code = <<<PHP
                        protected function getEnumClass(): string
                        {
                            return \\{$enumClass}::class;
                        }
            PHP;

        if ($enumClass !== $name) {
            $code .= <<<PHP


                        public function getName(): string
                        {
                            return '$name';
                        }
            PHP;
        }

        $baseClass = AbstractEnumType::class;
        $this->appendDefaultOnNullMethods($code, $enumClass, $defaultOnNull);

        return <<<PHP

            if (!\class_exists($classname::class)) {
                class $classname extends \\{$baseClass}
                {
        $code
                }
            }

        PHP;
    }

    private static function getMarker(): string
    {
        return 'ELAO_ENUM_DT_DBAL';
    }

    private function appendDefaultOnNullMethods(string & $code, string $enumClass, \BackedEnum|int|string|null $defaultOnNull): void
    {
        if ($defaultOnNull !== null) {
            $defaultOnNullCode = var_export(
                $defaultOnNull instanceof \BackedEnum ? $defaultOnNull->value : $defaultOnNull,
                true,
            );

            $code .= <<<PHP


                        protected function onNullFromDatabase(): ?\BackedEnum
                        {
                            return \\{$enumClass}::from($defaultOnNullCode);
                        }

                        protected function onNullFromPhp(): int|string|null
                        {
                            return {$defaultOnNullCode};
                        }
            PHP;
        }
    }
}
