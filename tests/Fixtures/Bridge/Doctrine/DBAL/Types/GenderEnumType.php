<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;

class GenderEnumType extends AbstractEnumType
{
    public const NAME = 'gender';

    protected function getEnumClass(): string
    {
        return Gender::class;
    }

    protected function onNullFromDatabase()
    {
        return Gender::get(Gender::UNKNOW);
    }

    protected function onNullFromPhp()
    {
        return Gender::UNKNOW;
    }

    public function getName()
    {
        return self::NAME;
    }
}
