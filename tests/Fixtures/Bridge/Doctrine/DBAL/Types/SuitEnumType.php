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

class SuitEnumType extends AbstractEnumType
{
    protected function getEnumClass(): string
    {
        return Suit::class;
    }
}
