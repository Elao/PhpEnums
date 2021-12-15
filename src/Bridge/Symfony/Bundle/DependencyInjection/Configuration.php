<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('elao_enum');

        $this->addDoctrineDbalSection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function addDoctrineDbalSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('doctrine')
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('type')
            ->children()
                ->arrayNode('types')
                    ->beforeNormalization()
                        ->always(static function (array $values): array {
                            // Allows reusing type name as the enum class implicitly
                            foreach ($values as $name => &$config) {
                                if (null === $config) {
                                    $config['class'] = $name;
                                    continue;
                                }

                                if (\is_array($config) && !isset($config['class'])) {
                                    $config['class'] = $name;
                                }
                            }

                            return $values;
                        })
                    ->end()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->beforeNormalization()
                        // Allows passing the class as string directly instead of the whole config array
                        ->ifString()->then(static function (string $v): array { return ['class' => $v]; })
                    ->end()
                    ->children()
                        ->scalarNode('class')
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifTrue(static function (string $class): bool { return !is_a($class, \BackedEnum::class, true); })
                                ->thenInvalid(sprintf('Invalid class. Expected instance of "%s"', \BackedEnum::class) . '. Got %s.')
                            ->end()
                        ->end()
                        ->variableNode('default')
                            ->info('Default enumeration case on NULL')
                            ->cannotBeEmpty()
                            ->defaultValue(null)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
