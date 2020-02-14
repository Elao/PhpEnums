<?php

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
    const MARKER = 'ELAO_ENUM_DT';

    public function dumpToFile(string $file, array $types)
    {
        file_put_contents($file, $this->dump($types));
    }

    private function dump(array $types): string
    {
        $code = "<?php\n";
        foreach ($types as [$class, $type, $name]) {
            $code .= $this->getTypeCode($class, $type, $name);
        }

        return $code;
    }

    private function getTypeCode(string $class, string $type, string $name): string
    {
        $baseClass = $type === 'int' ? AbstractIntegerEnumType::class : AbstractEnumType::class;
        $fqcn = self::getTypeClassname($class);
        $classname = basename(str_replace('\\', '/', $fqcn));
        $ns = substr($fqcn, 0, -\strlen($classname) - 1);

        return <<<PHP

namespace $ns;

if (!\class_exists('\\$fqcn')) {
    class $classname extends \\{$baseClass}
    {
        const NAME = '$name';

        protected function getEnumClass(): string
        {
            return \\{$class}::class;
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
