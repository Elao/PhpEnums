<?php

declare(strict_types=1);

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
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\RequestStatus;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

abstract class ElaoEnumExtensionTest extends TestCase
{
    protected const FIXTURES_PATH = __DIR__ . '/../../../../../Fixtures/Bridge/Symfony/Bundle/DependencyInjection/ElaoEnumExtension';

    public function testDefaults(): void
    {
        $container = $this->createContainerFromFile('defaults');

        self::assertFalse($container->hasParameter('.elao_enum.doctrine_types'), 'no doctrine type are registered');
    }

    public function testDoctrineTypes(): void
    {
        $container = $this->createContainerFromFile('doctrine_types');

        self::assertEquals([
            [Suit::class, 'scalar', 'suit', null],
            [Permissions::class, 'scalar', 'permissions', null],
            [RequestStatus::class, 'scalar', 'request_status', 200],
            [Suit::class, 'enum', 'suit_enum', null],
        ], $container->getParameter('.elao_enum.doctrine_types'));
    }

    public function testDoctrineTypesSQLEnum(): void
    {
        $container = $this->createContainerFromFile('doctrine_types_sql_enum');

        self::assertEquals([
            [Suit::class, 'enum', 'suit', null],
            [Permissions::class, 'scalar', 'permissions', null],
        ], $container->getParameter('.elao_enum.doctrine_types'));
    }

    public function testDoctrineTypesArePrepended(): void
    {
        $container = $this->createContainerFromFile('doctrine_types', false);
        /** @var ElaoEnumExtension $ext */
        $ext = $container->getExtension('elao_enum');
        $ext->prepend($container);

        self::assertEquals([
            [
                'dbal' => [
                    'types' => [
                        'suit' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\SuitType',
                        'permissions' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\PermissionsType',
                        'request_status' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\RequestStatusType',
                        'suit_enum' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\SuitEnumType',
                    ],
                    'mapping_types' => ['enum' => 'string'],
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
            'kernel.bundles' => [
                'DoctrineBundle' => DoctrineBundle::class,
            ],
            'kernel.cache_dir' => self::FIXTURES_PATH . '/cache_dir',
        ]));
    }
}
