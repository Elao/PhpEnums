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
use Elao\Enum\Tests\Fixtures\Enum\RequestStatus;

class RequestStatusType extends AbstractEnumType
{
    protected function getEnumClass(): string
    {
        return RequestStatus::class;
    }
}
