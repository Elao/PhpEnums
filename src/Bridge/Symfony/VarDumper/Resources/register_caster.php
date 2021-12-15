<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

use Elao\Enum\Bridge\Symfony\VarDumper\Caster\ReadableEnumCaster;
use Elao\Enum\ReadableEnumInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;

if (class_exists(VarCloner::class) && !isset(VarCloner::$defaultCasters[ReadableEnumInterface::class])) {
    VarCloner::$defaultCasters += [ReadableEnumInterface::class => [ReadableEnumCaster::class, 'castEnum']];
}
