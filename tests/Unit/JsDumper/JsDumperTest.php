<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\JsDumper;

use Elao\Enum\JsDumper\JsDumper;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class JsDumperTest extends TestCase
{
    /** @var string */
    private static $dumpDir;

    public static function setUpBeforeClass(): void
    {
        self::$dumpDir = self::getDumpDir();
    }

    protected function setUp(): void
    {
        $fs = new Filesystem();
        $fs->remove(self::$dumpDir);
    }

    /**
     * @dataProvider provide testNormalizePath data
     */
    public function testNormalizePath(string $path, string $baseDir = null, string $expectedPath): void
    {
        $dumper = new JsDumper('lib-path', $baseDir);

        self::assertSame($expectedPath, $dumper->normalizePath($path));
    }

    public function provide testNormalizePath data(): iterable
    {
        yield 'no base dir' => ['foo/enum.js', null, 'foo/enum.js'];
        yield 'with base dir' => ['enum.js', 'foo', 'foo/enum.js'];
        yield 'with absolute path' => ['/bar/enum.js', 'foo', '/bar/enum.js'];
        yield 'with ../ path' => ['../enum.js', 'foo/bar/', 'foo/bar/../enum.js'];
        yield 'with ./ path' => ['./enum.js', 'foo', './enum.js'];
    }

    /**
     * @dataProvider provide testDumpLibrary data
     */
    public function testDumpLibrary(string $libPath, string $baseDir = null, string $expectedPath): void
    {
        $dumper = new JsDumper($libPath, $baseDir);
        $dumper->dumpLibrarySources();

        self::assertStringEqualsFile(
            $expectedPath,
            JsDumper::DISCLAIMER . "\n\n" . file_get_contents(PACKAGE_ROOT_DIR . '/res/js/Enum.js')
        );
    }

    public function provide testDumpLibrary data(): iterable
    {
        $dumpDir = self::getDumpDir();

        yield 'no base dir' => ["$dumpDir/enum.js", null, "$dumpDir/enum.js"];
        yield 'with base dir' => ['enum.js', $dumpDir, "$dumpDir/enum.js"];
    }

    /**
     * @dataProvider provide testDumpEnumToFile data
     */
    public function testDumpEnumToFile(
        string $enumClass,
        string $path,
        string $libPath,
        string $baseDir = null,
        string $expectedPath,
        string $expectedImportPath
    ): void {
        $dumper = new JsDumper($libPath, $baseDir);
        $dumper->dumpLibrarySources();
        $dumper->dumpEnumToFile($enumClass, $path);

        self::assertFileExists($expectedPath);
        self::assertStringContainsString("import Enum from '$expectedImportPath'", file_get_contents($expectedPath));
    }

    public function provide testDumpEnumToFile data(): iterable
    {
        $dumpDir = self::getDumpDir();

        yield 'no base dir' => [
            'enumClass' => SimpleEnum::class,
            'path' => "$dumpDir/module/common/simple_enum.js",
            'libPath' => "$dumpDir/lib/enum.js",
            'baseDir' => null,
            'expectedPath' => "$dumpDir/module/common/simple_enum.js",
            'expectedImportPath' => '../../lib/enum',
        ];

        yield 'with base dir' => [
            'enumClass' => SimpleEnum::class,
            'path' => 'simple_enum.js',
            'libPath' => "$dumpDir/lib/enum.js",
            'baseDir' => "$dumpDir/module/common",
            'expectedPath' => "$dumpDir/module/common/simple_enum.js",
            'expectedImportPath' => '../../lib/enum',
        ];

        yield 'with same base dir for lib' => [
            'enumClass' => SimpleEnum::class,
            'path' => 'enums/simple_enum.js',
            'libPath' => 'lib/enum.js',
            'baseDir' => "$dumpDir/assets/js",
            'expectedPath' => "$dumpDir/assets/js/enums/simple_enum.js",
            'expectedImportPath' => '../lib/enum',
        ];
    }

    /**
     * @dataProvider provide testDumpEnumClass data
     * @requires OS Linux|Darwin
     */
    public function testDumpEnumClass(string $enumClass, string $expectationFilePath): void
    {
        $dumper = new JsDumper('enum.js');
        $js = $dumper->dumpEnumClass($enumClass);

        self::assertStringEqualsFile(FIXTURES_DIR . "/JsDumper/expected/$expectationFilePath", $js);
    }

    public function provide testDumpEnumClass data(): iterable
    {
        yield 'simple enum' => [
            'enumClass' => SimpleEnum::class,
            'expectationFilePath' => 'simple_enum.js',
        ];

        yield 'readable enum' => [
            'enumClass' => Gender::class,
            'expectationFilePath' => 'readable_enum.js',
        ];

        yield 'flagged enum' => [
            'enumClass' => Permissions::class,
            'expectationFilePath' => 'flagged_enum.js',
        ];
    }

    private static function getDumpDir(): string
    {
        return sys_get_temp_dir() . '/ElaoEnum/JsDumperTest';
    }
}
