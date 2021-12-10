<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Suit;

class SuitEnumTypeWithDefaultOnNull extends AbstractEnumType
{
    public const NAME = 'suit_with_default';

    protected function getEnumClass(): string
    {
        return Suit::class;
    }

    protected function onNullFromDatabase()
    {
        return Suit::Spades;
    }

    protected function onNullFromPhp()
    {
        return Suit::Spades->value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
