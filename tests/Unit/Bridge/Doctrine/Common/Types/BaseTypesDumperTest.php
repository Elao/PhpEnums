<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\Common\Types;

use Elao\Enum\Bridge\Doctrine\Common\AbstractTypesDumper;
use PHPUnit\Framework\TestCase;

abstract class BaseTypesDumperTest extends TestCase
{
    private readonly string $dumpPath;

    protected function setUp(): void
    {
        $this->dumpPath = sys_get_temp_dir() . '/elao_enum_types_dumper.php';
    }

    protected function tearDown(): void
    {
        @unlink($this->dumpPath);
    }

    public function testDumpToFile(): void
    {
        $this->getDumper()->dumpToFile($this->dumpPath, $this->getTypes());

        self::assertSnapshotFileMatchesFile($this->getSnapshotPath(), $this->dumpPath);
    }

    abstract protected function getSnapshotPath(): string;

    abstract protected function getTypes(): array;

    abstract protected function getDumper(): AbstractTypesDumper;

    private static function assertSnapshotFileMatchesFile(string $snapshotPath, string $actualPath)
    {
        self::updateSnapshot($snapshotPath, file_get_contents($actualPath));

        self::assertFileEquals($snapshotPath, $actualPath);
    }

    private static function updateSnapshot(string $snapshotPath, string $content): void
    {
        if (true === \defined('UPDATE_EXPECTATIONS') && true === \constant('UPDATE_EXPECTATIONS')) {
            file_put_contents($snapshotPath, rtrim($content) . "\n");
        }
    }
}
