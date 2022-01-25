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

use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum UnitSuit implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    case Hearts;
    case Diamonds;
    case Clubs;
    case Spades;

    public static function readables(): iterable
    {
        yield self::Hearts => 'suit.hearts';
        yield self::Diamonds => 'suit.diamonds';
        yield self::Clubs => 'suit.clubs';
        yield self::Spades => 'suit.spades';
    }
}
