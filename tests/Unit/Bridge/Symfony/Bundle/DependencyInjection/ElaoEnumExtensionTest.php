<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\ElaoEnumExtension;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

abstract class ElaoEnumExtensionTest extends TestCase
{
    const FIXTURES_PATH = __DIR__ . '/../../../../../Fixtures/Bridge/Symfony/Bundle/DependencyInjection/ElaoEnumExtension';

    public function testDefaults()
    {
        $container = $this->createContainerFromFile('defaults');

        self::assertTrue($container->hasDefinition(EnumNormalizer::class), 'normalizer is loaded');
        self::assertTrue($container->hasDefinition(EnumValueResolver::class), 'arg resolver is loaded');
        self::assertFalse($container->hasParameter('.elao_enum.doctrine_types'), 'no doctrine type are registered');
    }

    public function testDisabledArgValueResolver()
    {
        $container = $this->createContainerFromFile('disabled_arg_value_resolver');

        self::assertFalse($container->hasDefinition(EnumValueResolver::class), 'arg resolver is removed');
    }

    public function testDisabledSerializer()
    {
        $container = $this->createContainerFromFile('disabled_serializer');

        self::assertFalse($container->hasDefinition(EnumNormalizer::class), 'normalizer is removed');
    }

    public function testDoctrineTypes()
    {
        $container = $this->createContainerFromFile('doctrine_types');

        self::assertEquals([
            [Gender::class, 'string', 'gender'],
            [Permissions::class, 'int', 'permissions'],
        ], $container->getParameter('.elao_enum.doctrine_types'));
    }

    public function testDoctrineTypesArePrepended()
    {
        $container = $this->createContainerFromFile('doctrine_types', false);
        /** @var ElaoEnumExtension $ext */
        $ext = $container->getExtension('elao_enum');
        $ext->prepend($container);

        self::assertEquals([
            [
                'dbal' => [
                    'types' => [
                        'gender' => 'ELAO_ENUM_DT\\Elao\\Enum\\Tests\\Fixtures\\Enum\\GenderType',
                        'permissions' => 'ELAO_ENUM_DT\\Elao\\Enum\\Tests\\Fixtures\\Enum\\PermissionsType',
                    ],
                ],
            ],
        ], $container->getExtensionConfig('doctrine'));
    }

    protected function createContainerFromFile(string $file, bool $compile = true): ContainerBuilder
    {
        $container = $this->createContainer();
        $container->registerExtension(new ElaoEnumExtension());
        $this->loadFromFile($container, $file);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        if ($compile) {
            $container->compile();
        }

        return $container;
    }

    abstract protected function loadFromFile(ContainerBuilder $container, string $file);

    protected function createContainer(): ContainerBuilder
    {
        return new ContainerBuilder(new EnvPlaceholderParameterBag([
            'kernel.bundles' => ['DoctrineBundle' => DoctrineBundle::class],
            'kernel.cache_dir' => self::FIXTURES_PATH . '/cache_dir',
        ]));
    }
}
