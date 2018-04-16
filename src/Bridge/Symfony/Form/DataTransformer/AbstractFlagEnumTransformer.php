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
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;

abstract class AbstractFlagEnumTransformer implements DataTransformerInterface
{
    /** @var string|FlaggedEnum */
    protected $enumClass;

    public function __construct(string $enumClass)
    {
        if (!is_a($enumClass, FlaggedEnum::class, true)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not an instance of "%s"',
                $enumClass,
                FlaggedEnum::class
            ));
        }

        $this->enumClass = $enumClass;
    }

    protected function createEnum(int $value): FlaggedEnum
    {
        return \call_user_func([$this->enumClass, 'get'], $value);
    }

    protected function isAcceptableValueForEnum(int $value): bool
    {
        return \call_user_func([$this->enumClass, 'accepts'], $value);
    }
}
