<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection\Compiler;

use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Compiler\DoctrineODMTypesPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineODMTypesPassTest extends TestCase
{
    private readonly DoctrineODMTypesPass $pass;

    private readonly string $dumpPath;

    protected function setUp(): void
    {
        $this->dumpPath = sys_get_temp_dir() . '/elao_enum_types_dumper.php';
        $this->pass = new DoctrineODMTypesPass($this->dumpPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->dumpPath);
    }

    public function testDoesNothingOnNoTypesSet(): void
    {
        $container = new ContainerBuilder();
        $def = $container->register('doctrine_mongodb.odm.manager_configurator.abstract', \stdClass::class);

        $this->pass->process($container);

        self::assertNull($def->getFile());
        self::assertFileDoesNotExist($this->dumpPath);
    }

    public function testDumpsOnTypesSet(): void
    {
        $container = new ContainerBuilder();
        $def = $container->register('doctrine_mongodb.odm.manager_configurator.abstract', \stdClass::class);
        $container->getParameterBag()->set('.elao_enum.doctrine_mongodb_types', [
            ['Foo\Bar\Baz', 'single', 'baz', null],
            ['Foo\Bar\Qux', 'collection', 'qux', null],
        ]);

        $this->pass->process($container);

        self::assertSame($this->dumpPath, $def->getFile());
        self::assertFileExists($this->dumpPath);
    }
}
