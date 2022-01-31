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

class EnumProvider
{
    /**
     * @param array<string, class-string<\UnitEnum>> $aliases
     */
    public function __construct(private array $aliases = [])
    {
        foreach ($aliases as $enumClass) {
            $this->ensureEnumClass($enumClass);
        }
    }

    /**
     * Selects a random enum case.
     */
    public function randomEnum(string $enumClassOrAlias): \UnitEnum
    {
        /** @var \UnitEnum|string $class */
        $class = $this->aliases[$enumClassOrAlias] ?? $enumClassOrAlias;

        $this->ensureEnumClass($class);

        $cases = $class::cases();

        return $cases[mt_rand(0, \count($cases) - 1)];
    }

    /**
     * Selects given number of enum cases, between $count & $min.
     *
     * @param int  $count The max nb of cases to select
     * @param bool $exact If true, the exact $count of enums will be generated
     *                    (unless there is no more unique value available)
     * @param int  $min   The min nb of enums to generate, if not $exact
     *
     * @return \UnitEnum[]
     */
    public function randomEnums(string $enumClassOrAlias, int $count, bool $exact = false, int $min = 0): array
    {
        $selectedCases = [];

        if (!$exact) {
            $count = mt_rand($min, $count);
        }

        /** @var \UnitEnum|string $class */
        $class = $this->aliases[$enumClassOrAlias] ?? $enumClassOrAlias;

        $this->ensureEnumClass($class);

        $cases = $class::cases();

        while ($count > 0 && !empty($cases)) {
            $randomRank = mt_rand(0, \count($cases) - 1);
            $selectedCases[] = $cases[$randomRank];
            unset($cases[$randomRank]);
            $cases = array_values($cases);

            --$count;
        }

        return $selectedCases;
    }

    /**
     * @param class-string<\UnitEnum> $enumClass
     */
    private function ensureEnumClass(string $enumClass): void
    {
        if (!is_a($enumClass, \UnitEnum::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a proper enum class', $enumClass));
        }
    }
}
