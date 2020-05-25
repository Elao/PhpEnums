<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\FlaggedEnum;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SingleToCollectionFlagEnumTransformer extends AbstractFlagEnumTransformer
{
    /**
     * Transforms a FlaggedEnum objects to an array of bit flags.
     *
     * @param FlaggedEnum $value A FlaggedEnum instance
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return FlaggedEnum[]|null An array of FlaggedEnum instances with single bit flag
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
                \PHP_VERSION_ID >= 80000
                    ? get_debug_type($value)
                    : (\is_object($value) ? \get_class($value) : \gettype($value)
                )
            ));
        }

        return array_map(function ($flag) {
            return $this->createEnum($flag);
        }, $value->getFlags());
    }

    /**
     * Transforms an array of flagged enums to  single flagged enumeration instance.
     *
     * @param FlaggedEnum[] $values An array of flag enums
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return FlaggedEnum|null A single FlaggedEnum instance or null
     */
    public function reverseTransform($values)
    {
        if (null === $values) {
            return null;
        }

        if (!\is_array($values)) {
            throw new TransformationFailedException(sprintf(
                'Expected array. Got "%s".',
                \PHP_VERSION_ID >= 80000
                    ? get_debug_type($values)
                    : (\is_object($values) ? \get_class($values) : \gettype($values)
                )
            ));
        }

        if (0 === \count($values)) {
            return $this->createEnum(FlaggedEnum::NONE);
        }

        $rawValue = 0;
        foreach ($values as $value) {
            if (!$value instanceof FlaggedEnum) {
                throw new TransformationFailedException(sprintf(
                    'Expected array of "%s". Got a "%s" inside.',
                    $this->enumClass,
                    \PHP_VERSION_ID >= 80000
                        ? get_debug_type($value)
                        : (\is_object($value) ? \get_class($value) : \gettype($value)
                    )
                ));
            }
            $rawValue |= $value->getValue();
        }

        return $this->createEnum($rawValue);
    }
}
