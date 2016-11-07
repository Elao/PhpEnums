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

abstract class Enum implements EnumInterface
{
    /** @var mixed */
    protected $value;

    /**
     * The constructor is protected: use the static create method instead.
     *
     * @param mixed $value The raw value of an enumeration
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @return static A new instance of an enum
     */
    public static function create($value): EnumInterface
    {
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

        return static::create($value);
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
