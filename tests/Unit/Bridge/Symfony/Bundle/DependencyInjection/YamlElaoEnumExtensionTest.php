<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection;

use Elao\Enum\Tests\Fixtures\Enum\AnotherEnum;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlElaoEnumExtensionTest extends ElaoEnumExtensionTest
{
    protected function loadFromFile(ContainerBuilder $container, string $file)
    {
        $loader = new YamlFileLoader($container, new FileLocator(self::FIXTURES_PATH . '/yaml'));
        $loader->load($file . '.yaml');
    }

    /**
     * @group legacy
     */
    public function testDoctrineLegacyTypes()
    {
        $container = $this->createContainerFromFile('doctrine_types_legacy');

        self::assertEquals([
            [Gender::class, 'string', 'gender'],
            [AnotherEnum::class, 'enum', 'another'],
            [Permissions::class, 'int', 'permissions'],
        ], $container->getParameter('.elao_enum.doctrine_types'));
    }
}
