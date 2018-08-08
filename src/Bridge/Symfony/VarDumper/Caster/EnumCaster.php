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
use Elao\Enum\ReadableEnum;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\ConstStub;

final class EnumCaster
{
    public static function castEnum(EnumInterface $enum, $array)
    {
        $a = [];
        $value = $enum->getValue();
        $r = new \ReflectionClass($enum);

        $constants = array_filter($r->getConstants(), function (string $k) use ($r, $enum) {
            if (PHP_VERSION_ID >= 70100) {
                // ReflectionClass::getReflectionConstant() is only available since PHP 7.1
                $rConstant = $r->getReflectionConstant($k);
                $public = $rConstant->isPublic();
                $value = $rConstant->getValue();
            } else {
                $public = true;
                $value = \constant("{$r->getName()}::$k");
            }
            // Only keep public constants, for which value matches enumerable values set:
            return $public && $enum::accepts($value);
        }, ARRAY_FILTER_USE_KEY);

        $rConstants = array_flip($constants);

        // Append constant(s) name(s)
        $a[Caster::PREFIX_VIRTUAL . '⚑ '] = new ConstStub(implode(' | ', array_map(function ($v) use ($rConstants) {
            return $rConstants[$v];
        }, $enum instanceof FlaggedEnum && $enum->getFlags() ? $enum->getFlags() : (array) $value)), $value);

        // Append readable value
        if ($enum instanceof ReadableEnum) {
            $a[Caster::PREFIX_VIRTUAL . 'readable'] = $enum->getReadable();
        }

        // Append the instance value
        $a[Caster::PREFIX_PROTECTED . 'value'] = $value;

        // Append single bit flags list
        if ($enum instanceof FlaggedEnum) {
            $a[Caster::PREFIX_PROTECTED . 'flags'] = array_map(function (int $flag) use ($rConstants) {
                return new ConstStub($flag, $rConstants[$flag]);
            }, $enum->getFlags());
        }

        // Append any other potential properties
        return $a + $array;
    }
}
