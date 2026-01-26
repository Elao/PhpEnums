<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class XmlElaoEnumExtensionTest extends BaseElaoEnumExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(XmlFileLoader::class)) {
            $this->markTestSkipped('XML loader is not available');
        }
    }

    protected function loadFromFile(ContainerBuilder $container, string $file): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(self::FIXTURES_PATH . '/xml'));
        $loader->load($file . '.xml');
    }
}
