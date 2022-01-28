<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Faker\Provider;

use Elao\Enum\Exception\InvalidArgumentException;
use UnitEnum;

class EnumProvider
{
    /**
     * @param array<string, class-string<UnitEnum>> $enumMapping
     */
    public function __construct(private array $enumMapping = [])
    {
        foreach ($enumMapping as $enumClass) {
            $this->ensureEnumClass($enumClass);
        }
    }

    public function randomEnum(string $enumClassOrAlias): UnitEnum
    {
        /** @var UnitEnum|string $class */
        $class = $this->enumMapping[$enumClassOrAlias] ?? $enumClassOrAlias;
        $this->ensureEnumClass($class);

        $instances = $class::cases();

        return $instances[random_int(0, \count($instances) - 1)];
    }

    /**
     * @param class-string<UnitEnum> $enumClass
     */
    private function ensureEnumClass(string $enumClass): void
    {
        if (!is_a($enumClass, UnitEnum::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a proper enum class', $enumClass));
        }
    }
}
