<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\VarDumper\Caster;

use Elao\Enum\EnumInterface;
use Elao\Enum\FlaggedEnum;
use Elao\Enum\ReadableEnumInterface;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\ConstStub;

final class EnumCaster
{
    public static function castEnum(EnumInterface $enum, $array)
    {
        $a = [];
        $value = $enum->getValue();

        $originClasses = [\get_class($enum)];
        if (method_exists($enum, 'getDiscoveredClasses')) {
            $m = new \ReflectionMethod($enum, 'getDiscoveredClasses');
            $m->setAccessible(true);
            $originClasses = array_reverse($m->invoke(null));
        }

        $constants = [];
        foreach ($originClasses as $originClass) {
            $r = new \ReflectionClass($originClass);
            $constants = array_replace($constants, array_filter($r->getConstants(), static function (string $k) use ($r, $enum) {
                $rConstant = $r->getReflectionConstant($k);
                $public = $rConstant->isPublic();
                $value = $rConstant->getValue();
                // Only keep public constants, for which value matches enumerable values set:
                return $public && $enum::accepts($value);
            }, ARRAY_FILTER_USE_KEY));
        }

        $rConstants = array_flip($constants);

        // Append constant(s) name(s)
        $a[Caster::PREFIX_VIRTUAL . '⚑ '] = new ConstStub(implode(' | ', array_map(static function ($v) use ($rConstants) {
            return $rConstants[$v];
        }, $enum instanceof FlaggedEnum && $enum->getFlags() ? $enum->getFlags() : (array) $value)), $value);

        // Append readable value
        if ($enum instanceof ReadableEnumInterface) {
            $a[Caster::PREFIX_VIRTUAL . 'readable'] = $enum->getReadable();
        }

        // Append the instance value
        $a[Caster::PREFIX_PROTECTED . 'value'] = $value;

        // Append single bit flags list
        if ($enum instanceof FlaggedEnum) {
            $a[Caster::PREFIX_PROTECTED . 'flags'] = array_map(static function (int $flag) use ($rConstants) {
                return new ConstStub($flag, $rConstants[$flag]);
            }, $enum->getFlags());
        }

        // Append any other potential properties
        return $a + $array;
    }
}
