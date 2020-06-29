<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection;

use ApiPlatform\Core\JsonSchema\TypeFactory;
use Elao\Enum\Bridge\ApiPlatform\Core\JsonSchema\Type\ElaoEnumType;
use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use Elao\Enum\Bridge\Symfony\Console\Command\DumpJsEnumsCommand;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Elao\Enum\Bridge\Symfony\Translation\Extractor\EnumExtractor;
use Elao\Enum\FlaggedEnum;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ElaoEnumExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles['DoctrineBundle'])) {
            return;
        }

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        if (!($types = $config['doctrine']['types'] ?? false)) {
            return;
        }

        $doctrineTypesConfig = [];
        foreach ($types as $class => $value) {
            $doctrineTypesConfig[$value['name']] = TypesDumper::getTypeClassname($class);
        }

        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => $doctrineTypesConfig,
                'mapping_types' => ['enum' => 'string'],
            ],
        ]);
    }

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

        if (!$this->isConfigEnabled($container, $config['translation_extractor'])) {
            $container->removeDefinition(EnumExtractor::class);
        } else {
            $this->registerTranslationExtractorConfiguration($config['translation_extractor'], $container);
        }

        if (!class_exists(TypeFactory::class)) {
            $container->removeDefinition(ElaoEnumType::class);
        }

        $useEnumSQLDeclaration = $config['doctrine']['enum_sql_declaration'];
        if ($types = $config['doctrine']['types'] ?? false) {
            $defaultStringType = $useEnumSQLDeclaration ? 'enum' : 'string';
            $container->setParameter(
                '.elao_enum.doctrine_types',
                array_map(static function (string $class, array $v) use ($defaultStringType): array {
                    $type = $v['type'];

                    if (null === $type) {
                        $type = is_a($class, FlaggedEnum::class, true) ? $type = 'int' : $defaultStringType;
                    }

                    return [$class, $type, $v['name']];
                }, array_keys($types), $types)
            );
        }

        $jsEnums = $config['js'];
        $container->getDefinition(DumpJsEnumsCommand::class)
            ->replaceArgument(0, $jsEnums['paths'])
            ->replaceArgument(1, $jsEnums['base_dir'])
            ->replaceArgument(2, $jsEnums['lib_path'])
        ;
    }

    public function getNamespace()
    {
        return 'http://elao.com/schema/dic/elao_enum';
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    private function registerTranslationExtractorConfiguration(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition(EnumExtractor::class);

        $definition->replaceArgument(0, $config['paths']);
        $definition->replaceArgument(1, $config['domain']);
        $definition->replaceArgument(2, $config['filename_pattern']);
        $definition->replaceArgument(3, $config['ignore']);
    }
}
