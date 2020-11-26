<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\DBAL\Types;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use Elao\Enum\Tests\TestCase;

class TypesDumperTest extends TestCase
{
    public const FIXTURES_DIR = FIXTURES_DIR . '/Bridge/Doctrine/DBAL/Types/TypesDumperTest';

    /** @var TypesDumper */
    private $dumper;

    /** @var string */
    private $dumpPath;

    protected function setUp(): void
    {
        $this->dumper = new TypesDumper();
        $this->dumpPath = sys_get_temp_dir() . '/elao_enum_types_dumper.php';
    }

    protected function tearDown(): void
    {
        @unlink($this->dumpPath);
    }

    public function testDumpToFile(): void
    {
        $this->dumper->dumpToFile($this->dumpPath, [
            ['Foo\Bar\Baz', 'string', 'baz'],
            ['Foo\Bar\Xyz', 'enum', 'xyz'],
            ['Foo\Bar\Qux', 'int', 'qux'],
            ['Foo\Baz\Foo', 'int', 'foo'],
            ['Foo\Bar\Foo', 'json_collection', 'foo_json'],
            ['Foo\Bar\Foo', 'csv_collection', 'foo_csv'],
        ]);

        self::assertFileEquals(self::FIXTURES_DIR . '/dumped_types.php', $this->dumpPath);
    }
}
