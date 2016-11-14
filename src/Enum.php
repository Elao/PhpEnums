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
     * The constructor is private and cannot be overridden: use the static get method instead.
     *
     * @param mixed $value The raw value of an enumeration
     */
    final private function __construct($value)
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
    public static function get($value): EnumInterface
    {
        // Return the cached instance for given value if it already exists:
        if (null !== $instance = self::getCachedInstance($value)) {
            return $instance;
        }

        if (!static::accepts($value)) {
            throw new InvalidValueException($value, static::class);
        }

        return new static($value);
    }

    /**
     * Instantiates a new enumeration.
     *
     * @param string $name      The name of a particular enumerated constant
     * @param array  $arguments
     *
     * @throws \BadMethodCallException On invalid constant name
     *
     * @return static When $name is an existing constant for this enumeration type
     */
    public static function __callStatic($name, $arguments = []): EnumInterface
    {
        $value = @constant('static::' . $name);
        if (null === $value) {
            throw new \BadMethodCallException(sprintf(
                'No constant named "%s" exists in class "%s"',
                $name,
                static::class
            ));
        }

        return static::get($value);
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
            return static::get($value);
        }, static::values());
    }

    private static function getCachedInstance($value)
    {
        $enumType = static::class;
        $identifier = serialize($value);

        return self::$instances[$enumType][$identifier] ?? null;
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
