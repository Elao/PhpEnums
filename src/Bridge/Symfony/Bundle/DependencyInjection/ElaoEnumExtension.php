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

use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ElaoEnumExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');

        $bundles = $container->getParameter('kernel.bundles');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        if (isset($bundles['DoctrineBundle'])) {
            $this->prependDoctrineDbalConfig($config, $container);
        }
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        if ($types = $config['doctrine']['types'] ?? false) {
            $container->setParameter(
                '.elao_enum.doctrine_types',
                array_map(static function (string $name, array $v): array {
                    $default = $v['default'];

                    return [
                        $v['class'],
                        $name,
                        // Symfony DI parameters do not support enum cases (yet?).
                        // Does not fail in an array parameter, but the PhpDumper generate incorrect code for now.
                        $default instanceof \BackedEnum ? $default->value : $default,
                    ];
                }, array_keys($types), $types)
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
            $doctrineTypesConfig[$name] = TypesDumper::getTypeClassname($value['class']);
        }

        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => $doctrineTypesConfig,
            ],
        ]);
    }
}
