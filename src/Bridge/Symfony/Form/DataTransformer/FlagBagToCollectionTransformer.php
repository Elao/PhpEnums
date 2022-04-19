<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\FlagBag;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FlagBagToCollectionTransformer extends AbstractFlagBagTransformer
{
    /**
     * Transforms a FlagBag objects to an array of BackedEnum
     *
     * @param FlagBag $value A FlagBag instance
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return \BackedEnum[]|null An array of FlagBag instances with single bit flag
     */
    public function transform($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof FlagBag) {
            throw new TransformationFailedException(sprintf(
                'Expected instance of "%s". Got "%s".',
                FlagBag::class,
                get_debug_type($value)
            ));
        }

        if ($value->getType() !== $this->enumType) {
            throw new TransformationFailedException(sprintf(
                'Expected FlagBag instance of "%s" values. Got FlagBag instance of "%s" values.',
                $this->enumType,
                $value->getType()
            ));
        }

        return $value->getFlags();
    }

    /**
     * Transforms an array of BackedEnum to single FlagBag instance.
     *
     * @param \BackedEnum[] $values An array of flag enums
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return FlagBag|null A single FlagBag instance or null
     */
    public function reverseTransform($values): ?FlagBag
    {
        if (null === $values) {
            return null;
        }

        if (!\is_array($values)) {
            throw new TransformationFailedException(sprintf(
                'Expected array. Got "%s".',
                get_debug_type($values)
            ));
        }

        if (0 === \count($values)) {
            return $this->createEnum(FlagBag::NONE);
        }

        foreach ($values as $value) {
            if (!$value instanceof $this->enumType) {
                throw new TransformationFailedException(sprintf(
                    'Expected array of "%s". Got a "%s" inside.',
                    $this->enumType,
                    get_debug_type($value)
                ));
            }
        }

        return $this->createEnum($values);
    }
}
