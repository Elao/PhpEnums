<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection;

use ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle;
use ApiPlatform\Core\JsonSchema\TypeFactory;
use Elao\Enum\Bridge\ApiPlatform\Core\JsonSchema\Type\ElaoEnumType;
use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use Elao\Enum\Bridge\Symfony\Console\Command\DumpJsEnumsCommand;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Elao\Enum\Bridge\Symfony\Translation\Extractor\EnumExtractor;
use Elao\Enum\Bridge\Twig\Extension\EnumExtension;
use Elao\Enum\FlaggedEnum;
use Symfony\Bundle\TwigBundle\TwigBundle;
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
        foreach ($types as $name => $value) {
            $doctrineTypesConfig[$name] = TypesDumper::getTypeClassname($value['class'], $this->resolveDbalType(
                $value,
                $this->usesEnumSQLDeclaration($config)
            ));
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

        $bundles = $container->getParameter('kernel.bundles');

        if (!class_exists(TypeFactory::class) || !\in_array(ApiPlatformBundle::class, $bundles, true)) {
            $container->removeDefinition(ElaoEnumType::class);
        }

        if ($types = $config['doctrine']['types'] ?? false) {
            $container->setParameter(
                '.elao_enum.doctrine_types',
                array_map(function (string $name, array $v) use ($config): array {
                    return [$v['class'], $this->resolveDbalType($v, $this->usesEnumSQLDeclaration($config)), $name];
                }, array_keys($types), $types)
            );
        }

        if (!\in_array(TwigBundle::class, $bundles, true)) {
            $container->removeDefinition(EnumExtension::class);
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

    private function resolveDbalType(array $config, bool $useEnumSQLDeclaration): string
    {
        $type = $config['type'];
        $class = $config['class'];

        $defaultStringType = $useEnumSQLDeclaration ? 'enum' : 'string';

        if (null === $type) {
            $type = is_a($class, FlaggedEnum::class, true) ? $type = 'int' : $defaultStringType;
        }

        return $type;
    }

    private function usesEnumSQLDeclaration($config): bool
    {
        return $config['doctrine']['enum_sql_declaration'];
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
