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
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('enum_get', [$this, 'get']),
            new TwigFunction('enum_values', [$this, 'values']),
            new TwigFunction('enum_accepts', [$this, 'accepts']),
            new TwigFunction('enum_instances', [$this, 'instances']),
        ];
    }

    public function get(string $className, $value): EnumInterface
    {
        $this->esureEnum($className);

        return \call_user_func([$className, 'get'], $value);
    }

    public function values(string $className): array
    {
        $this->esureEnum($className);

        return \call_user_func([$className, 'values']);
    }

    public function accepts(string $className, $value): bool
    {
        $this->esureEnum($className);

        return \call_user_func([$className, 'accepts'], $value);
    }

    public function instances(string $className): array
    {
        $this->esureEnum($className);

        return \call_user_func([$className, 'instances']);
    }

    private function esureEnum(string $className)
    {
        if (!is_subclass_of($className, EnumInterface::class)) {
            throw new \Exception("$className is not an Enum.");
        }
    }
}
