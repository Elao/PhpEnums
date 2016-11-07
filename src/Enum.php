<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\InvalidValueException;
use Elao\Enum\Exception\LogicException;

abstract class Enum implements EnumInterface
{
    /**
     * Cached array of enum instances by enum type (FQCN).
     * This cache is used in order to make single enums values act as singletons.
     * This means you'll always get the exact same instance for a same enum value.
     *
     * @var array
     */
    private static $instances;

    /** @var mixed */
    protected $value;

    /**
     * The constructor is private: use the static create method instead.
     *
     * @param mixed $value The raw value of an enumeration
     */
    private function __construct($value)
    {
        $this->value = $value;

        $enumType = static::class;
        $identifier = serialize($value);

        if (isset(self::$instances[$enumType][$identifier])) {
            throw new LogicException(
                '"__construct" should not be called when an instance already exists for this enum value.'
            );
        }

        if (!isset(self::$instances[$enumType])) {
            self::$instances[$enumType] = [];
        }

        self::$instances[$enumType][$identifier] = $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return static The enum instance for given value
     */
    public static function create($value): EnumInterface
    {
        $enumType = static::class;
        $identifier = serialize($value);

        // Return the cached instance for given value if it already exists:
        if (isset(self::$instances[$enumType][$identifier])) {
            return self::$instances[$enumType][$identifier];
        }

        if (!static::accepts($value)) {
            throw new InvalidValueException($value, static::class);
        }

        return new static($value);
    }

    /**
     * {@inheritdoc}
     */
    public static function accepts($value): bool
    {
        return in_array($value, static::values(), true);
    }

    /**
     * {@inheritdoc}
     */
    public static function instances(): array
    {
        return array_map(function ($value) {
            return static::create($value);
        }, static::values());
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(EnumInterface $enum): bool
    {
        return get_class($this) === get_class($enum) && $this->value === $enum->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function is($value): bool
    {
        return $this->getValue() === $value;
    }
}
