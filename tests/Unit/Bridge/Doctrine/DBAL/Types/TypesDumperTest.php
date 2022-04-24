<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\Common\AbstractTypesDumper;
use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use Elao\Enum\Tests\Unit\Bridge\Doctrine\Common\Types\BaseTypesDumperTest;

class TypesDumperTest extends BaseTypesDumperTest
{
    protected function getSnapshotPath(): string
    {
        return __DIR__ . '/TypesDumperTest/dumped_types.php';
    }

    protected function getTypes(): array
    {
        return [
            ['Foo\Bar\Baz', 'scalar', 'Foo\Bar\Baz'],
            ['Foo\Bar\BazWithDefault', 'scalar', 'baz_with_default', 'foo'],
            ['Foo\Bar\Qux', 'scalar', 'Foo\Bar\Qux'],
            ['Foo\Baz\Foo', 'scalar', 'foo'],
            ['Foo\Baz\FooWithDefault', 'scalar', 'foo_with_default', 3],
            ['Foo\Bar\Baz', 'enum', 'foo_enum'],
            ['Foo\Bar\Baz', 'flagbag', 'foo_flagbag'],
            ['Foo\Bar\Baz', 'flagbag', 'foo_flagbag_with_default', 1],
        ];
    }

    protected function getDumper(): AbstractTypesDumper
    {
        return new TypesDumper();
    }
}
