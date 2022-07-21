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
use Elao\Enum\ExtrasTrait;

enum SuitWithCustomCase
{
    use ExtrasTrait;

    #[CustomCase(icon: 'fa-heart', color: 'red')]
    case Hearts;

    #[CustomCase(icon: 'fa-diamond', color: 'red')]
    case Diamonds;

    #[CustomCase(icon: 'fa-club', color: 'black')]
    case Clubs;

    #[CustomCase(icon: 'fa-spade', color: 'black')]
    case Spades;
}


#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
class CustomCase extends EnumCase
{
    public function __construct(string $icon, string $color)
    {
        parent::__construct(null, ['icon' => $icon, 'color' => $color]);
    }
}
