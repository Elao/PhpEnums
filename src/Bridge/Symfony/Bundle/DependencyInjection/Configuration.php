<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('elao_enum');
        $rootNode = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('elao_enum');

        $rootNode->children()
            ->arrayNode('argument_value_resolver')->canBeDisabled()->end()
            ->arrayNode('serializer')
                ->{interface_exists(SerializerInterface::class) ? 'canBeDisabled' : 'canBeEnabled'}()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
