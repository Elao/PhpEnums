<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types;

use Elao\Enum\Bridge\Doctrine\ODM\Types\AbstractCollectionEnumType;
use Elao\Enum\Tests\Fixtures\Enum\RequestStatus;

class RequestStatusCollectionType extends AbstractCollectionEnumType
{
    protected function getEnumClass(): string
    {
        return RequestStatus::class;
    }
}
