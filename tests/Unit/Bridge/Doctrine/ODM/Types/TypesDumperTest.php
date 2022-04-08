<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\ODM\Types;

use Elao\Enum\Bridge\Doctrine\Common\AbstractTypesDumper;
use Elao\Enum\Bridge\Doctrine\ODM\Types\TypesDumper;
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
            ['Foo\Bar\Baz', 'single', 'Foo\Bar\Baz'],
            ['Foo\Bar\Qux', 'single', 'Foo\Bar\Qux'],
            ['Foo\Baz\Foo', 'single', 'foo'],
            ['Foo\Baz\Foo', 'collection', 'foo_collection'],
        ];
    }

    protected function getDumper(): AbstractTypesDumper
    {
        return new TypesDumper();
    }
}
