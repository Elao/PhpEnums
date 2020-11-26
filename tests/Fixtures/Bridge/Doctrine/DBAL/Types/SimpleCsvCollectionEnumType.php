<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractCsvCollectionEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class SimpleCsvCollectionEnumType extends AbstractCsvCollectionEnumType
{
    public const NAME = 'simple_enum_csv_collection';

    protected function getEnumClass(): string
    {
        return SimpleEnum::class;
    }

    public function getName()
    {
        return self::NAME;
    }
}
