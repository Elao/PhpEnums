<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Twig\Extension;

use Elao\Enum\EnumInterface;
use Elao\Enum\Exception\InvalidArgumentException;
use Elao\Enum\ReadableEnumInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('enum_get', [$this, 'get']),
            new TwigFunction('enum_values', [$this, 'values']),
            new TwigFunction('enum_accepts', [$this, 'accepts']),
            new TwigFunction('enum_instances', [$this, 'instances']),
            new TwigFunction('enum_readables', [$this, 'readables']),
            new TwigFunction('enum_readable_for', [$this, 'readableFor']),
        ];
    }

    public function get(string $className, $value): EnumInterface
    {
        if (!is_a($className, EnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not an "%s".', $className, EnumInterface::class));
        }

        return \call_user_func([$className, 'get'], $value);
    }

    public function values(string $className): array
    {
        if (!is_a($className, EnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not an "%s".', $className, EnumInterface::class));
        }

        return \call_user_func([$className, 'values']);
    }

    public function accepts(string $className, $value): bool
    {
        if (!is_a($className, EnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not an "%s".', $className, EnumInterface::class));
        }

        return \call_user_func([$className, 'accepts'], $value);
    }

    public function instances(string $className): array
    {
        if (!is_a($className, EnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not an "%s".', $className, EnumInterface::class));
        }

        return \call_user_func([$className, 'instances']);
    }

    public function readables(string $className): array
    {
        if (!is_a($className, ReadableEnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a "%s".', $className, ReadableEnumInterface::class));
        }

        return \call_user_func([$className, 'readables']);
    }

    public function readableFor(string $className, $value): string
    {
        if (!is_a($className, ReadableEnumInterface::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a "%s".', $className, ReadableEnumInterface::class));
        }

        return \call_user_func([$className, 'readableFor'], $value);
    }
}
