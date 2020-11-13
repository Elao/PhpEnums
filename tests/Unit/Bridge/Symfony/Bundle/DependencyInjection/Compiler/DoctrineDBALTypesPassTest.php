<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection\Compiler;

use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Compiler\DoctrineDBALTypesPass;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineDBALTypesPassTest extends TestCase
{
    /** @var DoctrineDBALTypesPass */
    private $pass;

    /** @var string */
    private $dumpPath;

    protected function setUp(): void
    {
        $this->dumpPath = sys_get_temp_dir() . '/elao_enum_types_dumper.php';
        $this->pass = new DoctrineDBALTypesPass($this->dumpPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->dumpPath);
    }

    public function testDoesNothingOnNoTypesSet()
    {
        $container = new ContainerBuilder();
        $def = $container->register('doctrine.dbal.connection_factory', \stdClass::class);

        $this->pass->process($container);

        self::assertNull($def->getFile());
        self::assertFileDoesNotExist($this->dumpPath);
    }

    public function testDumpsOnTypesSet()
    {
        $container = new ContainerBuilder();
        $def = $container->register('doctrine.dbal.connection_factory', \stdClass::class);
        $container->getParameterBag()->set('.elao_enum.doctrine_types', [
            ['Foo\Bar\Baz', 'string', 'baz'],
            ['Foo\Bar\Qux', 'int', 'qux'],
        ]);

        $this->pass->process($container);

        self::assertSame($this->dumpPath, $def->getFile());
        self::assertFileExists($this->dumpPath);
    }
}
