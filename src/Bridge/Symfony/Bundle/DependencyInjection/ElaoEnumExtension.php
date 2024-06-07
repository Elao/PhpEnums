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

use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper as DBALTypesDumper;
use Elao\Enum\Bridge\Doctrine\ODM\Types\TypesDumper as ODMTypesDumper;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class ElaoEnumExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');

        $bundles = $container->getParameter('kernel.bundles');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        if (isset($bundles['DoctrineBundle'])) {
            $this->prependDoctrineDbalConfig($config, $container);
        }

        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $this->prependDoctrineOdmConfig($config, $container);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        // TODO: Use Symfony's 6.1 backed enum resolver once available:
        if (class_exists(\Symfony\Component\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver::class)) {
            $container->removeDefinition(BackedEnumValueResolver::class);
        }

        if ($types = $config['doctrine']['types'] ?? false) {
            $container->setParameter(
                '.elao_enum.doctrine_types',
                array_map(function (string $name, array $v) use ($config): array {
                    $default = $v['default'];

                    return [
                        $v['class'],
                        $this->resolveDbalType($v, $this->usesEnumSQLDeclaration($config)),
                        $name,
                        // Symfony DI parameters do not support enum cases (yet?).
                        // Does not fail in an array parameter, but the PhpDumper generate incorrect code for now.
                        $default instanceof \BackedEnum ? $default->value : $default,
                    ];
                }, array_keys($types), $types)
            );
        }

        if ($types = $config['doctrine_mongodb']['types'] ?? false) {
            $container->setParameter(
                '.elao_enum.doctrine_mongodb_types',
                array_map(static fn (string $name, array $v) => [$v['class'], $v['type'], $name], array_keys($types), $types)
            );
        }
    }

    public function getNamespace(): string
    {
        return 'http://elao.com/schema/dic/elao_enum';
    }

    public function getXsdValidationBasePath(): string
    {
        return __DIR__ . '/../config/schema';
    }

    private function prependDoctrineDbalConfig(array $config, ContainerBuilder $container): void
    {
        if (!($types = $config['doctrine']['types'] ?? false)) {
            return;
        }

        $doctrineTypesConfig = [];
        foreach ($types as $name => $value) {
            $doctrineTypesConfig[$name] = DBALTypesDumper::getTypeFullyQualifiedClassName($value['class'], $this->resolveDbalType(
                $value,
                $this->usesEnumSQLDeclaration($config)
            ), $name);
        }

        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => $doctrineTypesConfig,
                'mapping_types' => ['enum' => 'string'],
            ],
        ]);
    }

    private function prependDoctrineOdmConfig(array $config, ContainerBuilder $container): void
    {
        if (!($types = $config['doctrine_mongodb']['types'] ?? false)) {
            return;
        }

        $doctrineTypesConfig = [];
        foreach ($types as $name => $value) {
            $doctrineTypesConfig[$name] = ODMTypesDumper::getTypeFullyQualifiedClassName($value['class'], $value['type'], $name);
        }

        $container->prependExtensionConfig('doctrine_mongodb', ['types' => $doctrineTypesConfig]);
    }

    private function resolveDbalType(array $config, bool $useEnumSQLDeclaration): string
    {
        return $config['type'] ?? ($useEnumSQLDeclaration ? DBALTypesDumper::TYPE_ENUM : DBALTypesDumper::TYPE_SCALAR);
    }

    private function usesEnumSQLDeclaration(array $config): bool
    {
        return $config['doctrine']['enum_sql_declaration'];
    }
}
