<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection;

use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\ElaoEnumExtension;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
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

    protected function createContainerFromFile($file): ContainerBuilder
    {
        $container = $this->createContainer();
        $container->registerExtension(new ElaoEnumExtension());
        $this->loadFromFile($container, $file);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $container->compile();

        return $container;
    }

    abstract protected function loadFromFile(ContainerBuilder $container, string $file);

    protected function createContainer(): ContainerBuilder
    {
        return new ContainerBuilder(new EnvPlaceholderParameterBag([
            'kernel.bundles' => [],
            'kernel.cache_dir' => self::FIXTURES_PATH . '/cache_dir',
        ]));
    }
}
