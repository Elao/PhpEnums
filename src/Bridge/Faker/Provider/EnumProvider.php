<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Faker\Provider;

use Elao\Enum\EnumInterface;
use Elao\Enum\Exception\InvalidArgumentException;
use Elao\Enum\FlaggedEnum;

class EnumProvider
{
    /**
     * Enum mapping as an array with :
     *
     * - alias for Enum class as key
     * - Enum FQCN as value
     *
     * Example :
     *
     * ```
     * [
     *   'Civility' => Civility::class,
     *   'Gender'   => Gender::class,
     * ]
     * ```
     *
     * @var array
     */
    private $enumMapping = [];

    public function __construct(array $enumMapping = [])
    {
        foreach ($enumMapping as $enumAlias => $enumClass) {
            $this->ensureEnumClass($enumClass);
            $this->enumMapping[$enumAlias] = $enumClass;
        }
    }

    /**
     * @param string $enumValueShortcut As <ENUM_CLASS_ALIAS_OR_ENUM_FCQN>::<ENUM_VALUE_CONSTANT>
     *                                  Examples: 'Gender::MALE', 'App\Enum\Gender::FEMALE', 'Permissions::READ|WRITE', etc.
     *
     * @throws InvalidArgumentException When the alias part of $enumValueShortcut is not a valid alias
     */
    public function enum(string $enumValueShortcut): EnumInterface
    {
        [$enumClassOrAlias, $constants] = explode('::', $enumValueShortcut);

        /** @var EnumInterface|string $class */
        $class = $this->enumMapping[$enumClassOrAlias] ?? $enumClassOrAlias;
        $this->ensureEnumClass($class);

        $constants = explode('|', $constants);

        // Flagged Enum if $constants count is greater than one:
        if (\count($constants) > 1) {
            if (!is_a($class, FlaggedEnum::class, true)) {
                throw new InvalidArgumentException("$class is not a valid FlaggedEnum");
            }
            $value = 0;
            foreach ($constants as $constant) {
                $value |= \constant($class . '::' . $constant);
            }
        } else {
            $value = \constant($class . '::' . current($constants));
        }

        return $class::get($value);
    }

    /**
     * @throws InvalidArgumentException When $enumClassAlias is not a valid alias
     */
    public function randomEnum(string $enumClassOrAlias): EnumInterface
    {
        /** @var EnumInterface|string $class */
        $class = $this->enumMapping[$enumClassOrAlias] ?? $enumClassOrAlias;
        $this->ensureEnumClass($class);

        $instances = $class::instances();
        $randomRank = random_int(0, \count($instances) - 1);

        return $instances[$randomRank];
    }

    /**
     * Generate an array of unique enum instances between $count & $min.
     *
     * @param int  $count    The max nb of enums to generate
     * @param bool $variable If false, the exact $count of enums will be generated
     *                       (unless there is no more unique value available)
     * @param int  $min      The min nb of enums to generate, if variable
     *
     * @return EnumInterface[]
     */
    public function randomEnums(string $enumClassOrAlias, int $count, bool $variable = true, int $min = 0): array
    {
        $enums = [];

        if ($variable) {
            $count = random_int($min, $count);
        }

        /** @var EnumInterface|string $class */
        $class = $this->enumMapping[$enumClassOrAlias] ?? $enumClassOrAlias;
        $this->ensureEnumClass($class);

        $instances = $class::instances();

        while ($count > 0 && !empty($instances)) {
            $randomRank = random_int(0, \count($instances) - 1);
            $enums[] = $instances[$randomRank];
            unset($instances[$randomRank]);
            $instances = array_values($instances);

            --$count;
        }

        return $enums;
    }

    /**
     * Make sure that $enumClass is a proper Enum class. Throws exception otherwise.
     *
     * @throws InvalidArgumentException When $enumClass is not a class or is not a proper Enum
     */
    private function ensureEnumClass(string $enumClass)
    {
        if (!is_a($enumClass, EnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a proper enum class', $enumClass));
        }
    }
}
