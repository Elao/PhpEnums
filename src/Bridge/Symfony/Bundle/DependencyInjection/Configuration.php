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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $this->addDoctrineSection($rootNode);
        $this->addTranslationExtractorSection($rootNode);
        $this->addJsEnumsSection($rootNode);

        return $treeBuilder;
    }

    private function addDoctrineSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('doctrine')
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('type')
            ->children()
                ->booleanNode('enum_sql_declaration')
                    ->defaultValue(false)
                    ->info('If true, by default for string enumerations, generate DBAL types with an ENUM SQL declaration with enum values instead of a VARCHAR (Your platform must support it)')
                ->end()
                ->arrayNode('types')
                    ->beforeNormalization()
                        ->always(function ($values) {
                            $legacyFormat = false;
                            foreach ($values as $name => $config) {
                                if (class_exists($name)) {
                                    $legacyFormat = true;
                                    @trigger_error('Using enum FQCN as keys at path "elao_enum.doctrine.types" is deprecated. Provide the name as keys and add the "class" option for each entry instead.', E_USER_DEPRECATED);
                                    break;
                                }
                            }
                            $newValues = [];

                            if ($legacyFormat) {
                                foreach ($values as $name => $value) {
                                    if (\is_string($value)) {
                                        $newValues[$value] = $name;
                                    } else {
                                        $newValues[$value['name']] = $value + ['class' => $name];
                                    }
                                }
                            } else {
                                $newValues = $values;
                            }

                            return $newValues;
                        })
                    ->end()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->beforeNormalization()
                        ->ifString()->then(static function (string $v): array { return ['class' => $v]; })
                    ->end()
                    ->children()
                        ->scalarNode('class')
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifTrue(static function (string $class): bool {return !is_a($class, EnumInterface::class, true); })
                                ->thenInvalid(sprintf('Invalid class. Expected instance of "%s"', EnumInterface::class))
                            ->end()
                        ->end()
                        ->enumNode('type')
                            ->values(['enum', 'string', 'int', 'collection'])
                            ->info(<<<TXT
Which column definition to use and the way the enumeration values are stored in the database:
- string: VARCHAR
- enum: ENUM(...values) (Your platform must support it)
- int: INT
- collection: JSON

Default is either "string" or "enum", controlled by the `elao_enum.doctrine.enum_sql_declaration` option.
Default for flagged enums is "int".
TXT
                            )
                            ->cannotBeEmpty()
                            ->defaultValue(null)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    private function addTranslationExtractorSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
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
        ->end();
    }

    private function addJsEnumsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('js')
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('path')
                ->children()
                    ->scalarNode('base_dir')
                        ->info('A prefixed dir used for relative paths supplied for each of the generated enums and library path')
                        ->example('%kernel.project_dir%/assets/js/modules')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('lib_path')
                        ->info('The path of the file were to place the javascript library sources used by the dumped enums.')
                        ->example('%kernel.project_dir%/assets/js/lib/enum.js')
                        ->defaultNull()
                    ->end()
                    ->arrayNode('paths')
                        ->defaultValue([])
                        ->info('Path where to generate the javascript enums per enum class')
                        ->example(['App\Enum\SimpleEnum' => '"common/SimpleEnum.js"'])
                        ->useAttributeAsKey('class')
                        ->validate()
                            ->ifTrue(static function (array $v): bool {return self::hasNonEnumKeys($v); })
                            ->then(static function (array $v) { self::throwsNonEnumKeysException($v); })
                        ->end()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    private static function hasNonEnumKeys(array $values): bool
    {
        $classes = array_column($values, 'class');
        foreach ($classes as $class) {
            if (!is_a($class, EnumInterface::class, true)) {
                return true;
            }
        }

        return false;
    }

    private static function throwsNonEnumKeysException(array $values)
    {
        $classes = array_column($values, 'class');
        $invalids = [];
        foreach ($classes as $class) {
            if (!is_a($class, EnumInterface::class, true)) {
                $invalids[] = $class;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'Invalid classes %s. Expected instances of "%s"',
            json_encode($invalids),
            EnumInterface::class
        ));
    }
}
