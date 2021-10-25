<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractJsonCollectionEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class SimpleJsonCollectionEnumType extends AbstractJsonCollectionEnumType
{
    public const NAME = 'simple_enum_json_collection';

    protected function getEnumClass(): string
    {
        return SimpleEnum::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
