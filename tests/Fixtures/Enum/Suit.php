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

use Elao\Enum\ReadableEnumTrait;
use Elao\Enum\ReadableEnumInterface;

enum Suit: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';

    public static function readables(): array
    {
        return [
            self::Hearts->name => 'suit.hearts',
            self::Diamonds->name => 'suit.diamonds',
            self::Clubs->name => 'suit.clubs',
            self::Spades->name => 'suit.spades',
        ];
    }
}
