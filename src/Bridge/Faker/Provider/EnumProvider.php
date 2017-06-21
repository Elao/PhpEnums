<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
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

    public function __construct(array $enumMapping)
    {
        foreach ($enumMapping as $enumAlias => $enumClass) {
            $this->ensureEnumClass($enumClass);
            $this->enumMapping[$enumAlias] = $enumClass;
        }
    }

    /**
     * @param string $enumValueShortcut As <ENUM_CLASS_ALIAS>::<ENUM_VALUE_CONSTANT>
     *                                  Examples: 'Gender::MALE', 'Gender::FEMALE', 'Permissions::READ|WRITE', etc.
     *
     * @throws InvalidArgumentException When the alias part of $enumValueShortcut is not a valid alias
     *
     * @return EnumInterface
     */
    public function enum(string $enumValueShortcut): EnumInterface
    {
        list($enumClassAlias, $constants) = explode('::', $enumValueShortcut);

        if (!array_key_exists($enumClassAlias, $this->enumMapping)) {
            throw new InvalidArgumentException("$enumClassAlias is not a valid alias");
        }

        /** @var EnumInterface $class */
        $class = $this->enumMapping[$enumClassAlias];

        $constants = explode('|', $constants);

        // Flagged Enum if $constants count is greater than one:
        if (count($constants) > 1) {
            if (!is_a($class, FlaggedEnum::class, true)) {
                throw new InvalidArgumentException("$class is not a valid FlaggedEnum");
            }
            $value = 0;
            foreach ($constants as $constant) {
                $value |= constant($class . '::' . $constant);
            }
        } else {
            $value = constant($class . '::' . current($constants));
        }

        return $class::get($value);
    }

    /**
     * @param string $enumClassAlias
     *
     * @throws InvalidArgumentException When $enumClassAlias is not a valid alias
     *
     * @return EnumInterface
     */
    public function randomEnum(string $enumClassAlias): EnumInterface
    {
        if (!array_key_exists($enumClassAlias, $this->enumMapping)) {
            throw new InvalidArgumentException("$enumClassAlias is not a valid alias");
        }

        /** @var EnumInterface $class */
        $class = $this->enumMapping[$enumClassAlias];

        $instances = $class::instances();
        $randomRank = rand(0, count($instances) - 1);

        return $instances[$randomRank];
    }

    /**
     * Make sure that $enumClass is a proper Enum class. Throws exception otherwise.
     *
     * @param string $enumClass
     *
     * @throws InvalidArgumentException When $enumClass is not a class or is not a proper Enum
     */
    private function ensureEnumClass(string $enumClass)
    {
        if (!is_a($enumClass, EnumInterface::class, true)) {
            throw new InvalidArgumentException("$enumClass is not a proper enum class");
        }
    }
}
