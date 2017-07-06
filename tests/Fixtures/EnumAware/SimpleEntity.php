<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\EnumAware;

use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

class SimpleEntity
{
    /** @var Permissions */
    public $permissions;

    /** @var SimpleEnum */
    public $simpleEnum;
}
