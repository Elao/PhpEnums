<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

final class AnotherEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const SOMETHING = 'something';
    public const SOMETHING_ELSE = 'something_else';
    public const ANOTHER_THING = 'another_thing';
}
