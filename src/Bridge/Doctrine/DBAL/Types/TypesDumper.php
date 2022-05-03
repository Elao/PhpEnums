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
use Elao\Enum\Exception\LogicException;

/**
 * @internal
 */
class TypesDumper extends AbstractTypesDumper
{
    public const TYPE_SCALAR = 'scalar';
    public const TYPE_ENUM = 'enum';
    public const TYPE_FLAGBAG = 'flagbag';
    public const TYPES = [
        self::TYPE_SCALAR,
        self::TYPE_ENUM,
        self::TYPE_FLAGBAG,
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

        $baseClass = match ($type) {
            self::TYPE_SCALAR => AbstractEnumType::class,
            self::TYPE_ENUM => AbstractEnumSQLDeclarationType::class,
            self::TYPE_FLAGBAG => AbstractFlagBagType::class,
            default => throw new LogicException(sprintf('Unexpected type "%s"', $type)),
        };

        $this->appendDefaultOnNullMethods($code, $type, $enumClass, $defaultOnNull);

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

    private function appendDefaultOnNullMethods(string &$code, string $type, string $enumClass, \BackedEnum|int|string|null $defaultOnNull): void
    {
        if ($defaultOnNull !== null) {
            $defaultOnNullCode = var_export(
                $defaultOnNull instanceof \BackedEnum ? $defaultOnNull->value : $defaultOnNull,
                true,
            );

            if ($type == self::TYPE_FLAGBAG) {
                $code .= <<<PHP


                            protected function onNullFromDatabase(): ?\\Elao\\Enum\\FlagBag
                            {
                                return new \\Elao\\Enum\\FlagBag('$enumClass', $defaultOnNullCode);
                            }

                            protected function onNullFromPhp(): int|null
                            {
                                return {$defaultOnNullCode};
                            }
                PHP;
            } else {
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

    protected static function getSuffixes(): array
    {
        return [
            self::TYPE_SCALAR => 'Type',
            self::TYPE_ENUM => 'Type',
            self::TYPE_FLAGBAG => 'FlagBagType',
        ];
    }
}
