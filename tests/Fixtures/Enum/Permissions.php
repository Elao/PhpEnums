<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\FlagEnumInterface;

enum Permissions: int implements FlagEnumInterface
{
    case Execute = 1;
    case Write = 2;
    case Read = 4;
}
