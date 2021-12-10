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

use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use PHPUnit\Framework\TestCase;

class TypesDumperTest extends TestCase
{
    public const FIXTURES_DIR = __DIR__ . '/TypesDumperTest/';

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
            ['Foo\Bar\Baz', 'Foo\Bar\Baz'],
            ['Foo\Bar\BazWithDefault', 'baz_with_default', 'foo'],
            ['Foo\Bar\Qux', 'Foo\Bar\Qux'],
            ['Foo\Baz\Foo', 'foo'],
            ['Foo\Baz\FooWithDefault', 'foo_with_default', 3],
        ]);

        self::assertSnapshotFileMatchesFile(self::FIXTURES_DIR . '/dumped_types.php', $this->dumpPath);
    }

    private static function assertSnapshotFileMatchesFile(string $snapshotPath, string $actualPath)
    {
        self::updateSnapshot($snapshotPath, file_get_contents($actualPath));

        self::assertFileEquals(self::FIXTURES_DIR . '/dumped_types.php', $actualPath);
    }

    private static function updateSnapshot(string $snapshotPath, string $content): void
    {
        if (true === \defined('UPDATE_EXPECTATIONS') && true === \constant('UPDATE_EXPECTATIONS')) {
            file_put_contents($snapshotPath, rtrim($content) . "\n");
        }
    }
}
