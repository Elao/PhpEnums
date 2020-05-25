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

class BitmaskToBitFlagsValueTransformer extends AbstractFlagEnumTransformer
{
    /**
     * Transforms a FlaggedEnum objects to an array of bit flags.
     *
     * @param int $bitmask A single bit mask
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return int[]|null An array of bit flags
     */
    public function transform($bitmask)
    {
        if ($bitmask === null) {
            return null;
        }

        if (!\is_int($bitmask)) {
            throw new TransformationFailedException(sprintf(
                'Expected integer. Got "%s".',
                \PHP_VERSION_ID >= 80000
                    ? get_debug_type($bitmask)
                    : (\is_object($bitmask) ? \get_class($bitmask) : \gettype($bitmask)
                )
            ));
        }

        if (!$this->isAcceptableValueForEnum($bitmask)) {
            throw new TransformationFailedException(sprintf(
                'Invalid bitmask %s for "%s" flagged enum.',
                $bitmask,
                $this->enumClass
            ));
        }

        return $this->createEnum($bitmask)->getFlags();
    }

    /**
     * Transforms an array of flagged enums to  single flagged enumeration instance.
     *
     * @param int[] $flags bit flags
     *
     * @throws TransformationFailedException When the transformation fails
     *
     * @return int|null a bit mask
     */
    public function reverseTransform($flags)
    {
        if (null === $flags) {
            return null;
        }

        if (!\is_array($flags)) {
            throw new TransformationFailedException(sprintf(
                'Expected array. Got "%s".',
                \PHP_VERSION_ID >= 80000
                    ? get_debug_type($flags)
                    : (\is_object($flags) ? \get_class($flags) : \gettype($flags)
                )
            ));
        }

        if (0 === \count($flags)) {
            return FlaggedEnum::NONE;
        }

        $bitmask = 0;
        foreach ($flags as $flag) {
            if (!\is_int($flag)) {
                throw new TransformationFailedException(sprintf(
                    'Expected array of integers. Got a "%s" inside.',
                    \PHP_VERSION_ID >= 80000
                        ? get_debug_type($flag)
                        : (\is_object($flag) ? \get_class($flag) : \gettype($flag)
                    )
                ));
            }
            $bitmask |= $flag;
        }

        if (!$this->isAcceptableValueForEnum($bitmask)) {
            throw new TransformationFailedException(sprintf(
                'Invalid bitmask %s for "%s" flagged enum.',
                $bitmask,
                $this->enumClass
            ));
        }

        return $bitmask;
    }
}
