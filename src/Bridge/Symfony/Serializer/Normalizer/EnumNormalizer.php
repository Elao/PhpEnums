<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Serializer\Normalizer;

use Elao\Enum\EnumInterface;
use Elao\Enum\Exception\InvalidValueException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @final
 */
class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     *
     * @template T of int|string
     *
     * @param EnumInterface<T> $object
     *
     * @return T
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof EnumInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = []): EnumInterface
    {
        try {
            if (
                // Same as Symfony's AbstractObjectNormalizer:
                // for XML and CSV formats, when detecting something resembling a number,
                // attempt to cast it to integer as it might be an integer based enum.
                \is_string($data) &&
                \in_array($format, [CsvEncoder::FORMAT, XmlEncoder::FORMAT], true) &&
                (ctype_digit($data) || ('-' === $data[0] && ctype_digit(substr($data, 1))))
            ) {
                $data = (int) $data;
            }

            return \call_user_func([$class, 'get'], $data);
        } catch (InvalidValueException $e) {
            throw new UnexpectedValueException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return is_a($type, EnumInterface::class, true);
    }
}
