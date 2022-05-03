<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum Permissions: int implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('Execute')]
    case Execute = 1 << 0;
    #[EnumCase('Write')]
    case Write = 1 << 1;
    #[EnumCase('Read')]
    case Read = 1 << 2;
}
