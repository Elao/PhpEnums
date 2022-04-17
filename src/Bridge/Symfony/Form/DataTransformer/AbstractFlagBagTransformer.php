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

use Elao\Enum\Exception\InvalidArgumentException as ElaoInvalidArgumentException;
use Elao\Enum\Exception\LogicException;
use Elao\Enum\FlagBag;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;

abstract class AbstractFlagBagTransformer implements DataTransformerInterface
{
    /** @var class-string<\BackedEnum> */
    protected string $enumType;

    /** @var class-string<\BackedEnum> */
    public function __construct(string $enumType)
    {
        if (!is_a($enumType, \BackedEnum::class, true)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not an instance of "%s"',
                $enumType,
                \BackedEnum::class
            ));
        }

        try {
            FlagBag::getBitmask($enumType);
        } catch (LogicException $e) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not a valid bitmask enum',
                $enumType
            ), 0, $e);
        } catch (ElaoInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), 0, $e);
        }

        $this->enumType = $enumType;
    }

    /**
     * @param int|\BackedEnum[] $value
     */
    protected function createEnum(int|array $value): FlagBag
    {
        if (\is_int($value)) {
            return new FlagBag($this->enumType, $value);
        } else {
            return FlagBag::from($this->enumType, ...$value);
        }
    }

    protected function isAcceptableValueForEnum(int $value): bool
    {
        return FlagBag::accepts($this->enumType, $value);
    }
}
