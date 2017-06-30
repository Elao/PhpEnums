<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

final class Php71AutoDiscoveredEnum extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const FOO = 'foo';
    public const BAR = 'bar';
    public const BAZ = 'baz';

    private const PRIVATE = 'private';
    protected const PROTECTED = 'protected';
}
