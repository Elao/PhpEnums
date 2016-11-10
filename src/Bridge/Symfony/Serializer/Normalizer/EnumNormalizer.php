<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Serializer\Normalizer;

use Elao\Enum\EnumInterface;
use Elao\Enum\Exception\InvalidValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param EnumInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EnumInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        try {
            return call_user_func([$class, 'get'], $data);
        } catch (InvalidValueException $e) {
            throw new UnexpectedValueException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, EnumInterface::class, true);
    }
}
