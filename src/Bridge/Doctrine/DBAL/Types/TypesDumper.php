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

use Elao\Enum\Bridge\Doctrine\Common\AbstractTypesDumper;

/**
 * @internal
 */
class TypesDumper extends AbstractTypesDumper
{
    public const TYPE_SINGLE = 'single';
    public const TYPES = [
        self::TYPE_SINGLE,
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

    protected static function getMarker(): string
    {
        return 'ELAO_ENUM_DT_DBAL';
    }

    private function appendDefaultOnNullMethods(string &$code, string $enumClass, \BackedEnum|int|string|null $defaultOnNull): void
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

    protected static function getSuffixes(): array
    {
        return [
            self::TYPE_SINGLE => 'Type',
        ];
    }
}
