<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection;

use Elao\Enum\EnumInterface;
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
            ->arrayNode('doctrine')
                ->fixXmlConfig('type')
                ->children()
                    ->arrayNode('types')
                        ->validate()
                            ->ifTrue(static function (array $v): bool {
                                $classes = array_keys($v);
                                foreach ($classes as $class) {
                                    if (!is_a($class, EnumInterface::class, true)) {
                                        return true;
                                    }
                                }

                                return false;
                            })
                            ->then(static function (array $v) {
                                $classes = array_keys($v);
                                $invalids = [];
                                foreach ($classes as $class) {
                                    if (!is_a($class, EnumInterface::class, true)) {
                                        $invalids[] = $class;
                                    }
                                }

                                throw new \InvalidArgumentException(sprintf(
                                    'Invalid classes %s. Expected instances of "%s"',
                                    json_encode($invalids),
                                    EnumInterface::class)
                                );
                            })
                        ->end()
                            ->useAttributeAsKey('class')
                            ->arrayPrototype()
                            ->beforeNormalization()
                                ->ifString()->then(static function (string $v): array { return ['name' => $v]; })
                            ->end()
                            ->children()
                                ->scalarNode('name')->cannotBeEmpty()->end()
                                ->enumNode('type')->values(['string', 'int'])->cannotBeEmpty()->defaultValue('string')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('serializer')
                ->{interface_exists(SerializerInterface::class) ? 'canBeDisabled' : 'canBeEnabled'}()
            ->end()
            ->arrayNode('translation_extractor')
                ->canBeEnabled()
                ->fixXmlConfig('path')
                ->children()
                    ->arrayNode('paths')
                        ->example(['App\Enum' => '%kernel.project_dir%/src/Enum'])
                        ->useAttributeAsKey('namespace')
                        ->scalarPrototype()
                        ->end()
                    ->end()
                    ->scalarNode('domain')
                        ->defaultValue('messages')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('filename_pattern')
                        ->example('*Enum.php')
                        ->defaultValue('*.php')
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('ignore')
                        ->example(['%kernel.project_dir%/src/Enum/Other/*'])
                        ->scalarPrototype()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
