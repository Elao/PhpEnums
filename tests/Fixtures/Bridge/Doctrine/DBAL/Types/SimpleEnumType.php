<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractIntegerEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class SimpleEnumType extends AbstractIntegerEnumType
{
    const NAME = 'simple_enum';

    protected function getEnumClass() : string
    {
        return SimpleEnum::class;
    }

    protected function onNullFromDatabase()
    {
        return SimpleEnum::create(SimpleEnum::ZERO);
    }

    protected function onNullFromPhp()
    {
        return SimpleEnum::ZERO;
    }

    public function getName()
    {
        return self::NAME;
    }
}
