<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\VarDumper\Caster;

use Elao\Enum\FlagBag;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\ConstStub;

final class FlagBagCaster
{
    public static function cast(FlagBag $bag)
    {
        $a = [];

        // Append constant(s) name(s)
        $a[Caster::PREFIX_VIRTUAL . '⚑ '] = new ConstStub(
            implode(' | ', array_map(static fn (\BackedEnum $flag) => $flag->name, $bag->getFlags()) ?: ['NONE'])
        );
        $a[Caster::PREFIX_VIRTUAL . 'type'] = $bag->getType();
        $a[Caster::PREFIX_VIRTUAL . 'value'] = $bag->getValue();
        $a[Caster::PREFIX_VIRTUAL . 'bits'] = $bag->getBits();
        $a[Caster::PREFIX_VIRTUAL . 'flags'] = $bag->getFlags();

        return $a;
    }
}
