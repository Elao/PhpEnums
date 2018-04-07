<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\EnumInterface;
use Elao\Enum\Exception\InvalidValueException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ScalarToEnumTransformer implements DataTransformerInterface
{
    /** @var string|EnumInterface */
    private $enumClass;

    public function __construct(string $enumClass)
    {
        if (!is_a($enumClass, EnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not an instance of "%s"',
                $enumClass,
                EnumInterface::class
            ));
        }

        $this->enumClass = $enumClass;
    }

    /**
     * Transforms EnumInterface object to a scalar value.
     *
     * @param EnumInterface $value EnumInterface instance
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return int|string Value of EnumInterface
     */
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof $this->enumClass) {
            throw new TransformationFailedException(sprintf(
                'Expected instance of "%s". Got "%s".',
                $this->enumClass,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->getValue();
    }

    /**
     * Transforms scalar enum value to enumeration instance.
     *
     * @param int|string $value Value accepted by EnumInterface
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return EnumInterface|null A single FlaggedEnum instance or null
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        try {
            return $this->enumClass::get($value);
        } catch (InvalidValueException $exception) {
            throw new TransformationFailedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
