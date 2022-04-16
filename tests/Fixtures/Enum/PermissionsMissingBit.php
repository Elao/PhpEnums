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

enum PermissionsMissingBit: int
{
    case Execute = 1 << 0;
    case Write = 1 << 2;
    case Read = 1 << 3;
}
