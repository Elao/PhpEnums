<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
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
        $constants = $r->getConstants();

        if (PHP_VERSION_ID >= 70100) {
            $constants = array_filter($constants, function (string $k) use ($r) {
                return $r->getReflectionConstant($k)->isPublic();
            }, ARRAY_FILTER_USE_KEY);
        }

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
