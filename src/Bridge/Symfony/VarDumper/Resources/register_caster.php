<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

use Elao\Enum\Bridge\Symfony\VarDumper\Caster\EnumCaster;
use Elao\Enum\EnumInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;

if (class_exists(VarCloner::class) && !isset(VarCloner::$defaultCasters[EnumInterface::class])) {
    VarCloner::$defaultCasters += [EnumInterface::class => [EnumCaster::class, 'castEnum']];
}
