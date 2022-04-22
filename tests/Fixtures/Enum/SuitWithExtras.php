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

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ExtrasInterface;
use Elao\Enum\ExtrasTrait;

enum SuitWithExtras implements ExtrasInterface
{
    use ExtrasTrait;

    #[EnumCase(extras: ['icon' => 'fa-heart', 'color' => 'red', 'only-for-hearts' => 'value'])]
    case Hearts;

    #[EnumCase(extras: ['icon' => 'fa-diamond', 'color' => 'red'])]
    case Diamonds;

    #[EnumCase(extras: ['icon' => 'fa-club', 'color' => 'black'])]
    case Clubs;

    #[EnumCase(extras: ['icon' => 'fa-spade', 'color' => 'black'])]
    case Spades;
}
