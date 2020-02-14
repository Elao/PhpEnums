<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection;

use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ElaoEnumExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        if (!$this->isConfigEnabled($container, $config['argument_value_resolver'])) {
            $container->removeDefinition(EnumValueResolver::class);
        }

        if (!$this->isConfigEnabled($container, $config['serializer'])) {
            $container->removeDefinition(EnumNormalizer::class);
        }
    }

    public function getNamespace()
    {
        return 'http://elao.com/schema/dic/elao_enum';
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }
}
