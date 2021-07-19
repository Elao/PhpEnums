<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types;

use Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class SimpleEnumType extends AbstractEnumType
{
    protected function getEnumClass(): string
    {
        return SimpleEnum::class;
    }
}
