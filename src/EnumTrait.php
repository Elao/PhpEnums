<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\InvalidValueException;
use Elao\Enum\Exception\LogicException;

/**
 * @see EnumInterface which this trait implements
 */
trait EnumTrait
{
    /**
     * Cached array of enum instances by enum type (FQCN).
     * This cache is used in order to make single enums values act as singletons.
     * This means you'll always get the exact same instance for a same enum value.
     *
     * @var array
     *
     * @internal
     */
    private static $instances;

    /** @var int|string */
    protected $value;

    /**
     * The constructor is private and MUST NOT be overridden: use the static get method instead.
     *
     * @param int|string $value The raw value of an enumeration
     *
     * @internal Use {@see EnumInterface::get()} instead.
     * @final It must not be overridden in classes using this trait
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
        if (null !== $instance = self::$instances[static::class][serialize($value)] ?? null) {
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
        if (!\defined('static::' . $name)) {
            throw new \BadMethodCallException(sprintf(
                'No constant named "%s" exists in class "%s"',
                $name,
                static::class
            ));
        }

        return static::get(\constant('static::' . $name));
    }

    /**
     * {@inheritdoc}
     */
    public static function accepts($value): bool
    {
        return \in_array($value, static::values(), true);
    }

    /**
     * {@inheritdoc}
     */
    public static function instances(): array
    {
        return array_map(static function ($value) {
            return static::get($value);
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
        return \get_class($this) === \get_class($enum) && $this->value === $enum->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function is($value): bool
    {
        return $this->getValue() === $value;
    }
}
