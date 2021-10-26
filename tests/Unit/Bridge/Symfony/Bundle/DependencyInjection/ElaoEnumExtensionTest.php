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
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\ElaoEnumExtension;
use Elao\Enum\Bridge\Symfony\Console\Command\DumpJsEnumsCommand;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\EnumValueResolver;
use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Elao\Enum\Bridge\Symfony\Translation\Extractor\EnumExtractor;
use Elao\Enum\Tests\Fixtures\Enum\AnotherEnum;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;

abstract class ElaoEnumExtensionTest extends TestCase
{
    protected const FIXTURES_PATH = __DIR__ . '/../../../../../Fixtures/Bridge/Symfony/Bundle/DependencyInjection/ElaoEnumExtension';

    public function testDefaults(): void
    {
        $container = $this->createContainerFromFile('defaults');

        self::assertTrue($container->hasDefinition(EnumNormalizer::class), 'normalizer is loaded');
        self::assertTrue($container->hasDefinition(EnumValueResolver::class), 'arg resolver is loaded');
        self::assertFalse($container->hasParameter('.elao_enum.doctrine_types'), 'no doctrine type are registered');
        self::assertFalse($container->hasDefinition(EnumExtractor::class), 'translation extractor is removed');
        self::assertTrue($container->hasDefinition(DumpJsEnumsCommand::class), 'dump js command is loaded');
    }

    public function testDisabledArgValueResolver(): void
    {
        $container = $this->createContainerFromFile('disabled_arg_value_resolver');

        self::assertFalse($container->hasDefinition(EnumValueResolver::class), 'arg resolver is removed');
    }

    public function testDisabledSerializer(): void
    {
        $container = $this->createContainerFromFile('disabled_serializer');

        self::assertFalse($container->hasDefinition(EnumNormalizer::class), 'normalizer is removed');
    }

    public function testDoctrineTypes(): void
    {
        $container = $this->createContainerFromFile('doctrine_types');

        self::assertEquals([
            [Gender::class, 'string', 'gender', null],
            [AnotherEnum::class, 'enum', 'another', null],
            [Permissions::class, 'int', 'permissions', null],
            [SimpleEnum::class, 'json_collection', 'simple_collection_json', null],
            [SimpleEnum::class, 'csv_collection', 'simple_collection_csv', null],
        ], $container->getParameter('.elao_enum.doctrine_types'));
    }

    public function testDoctrineTypesDefaultType(): void
    {
        $container = $this->createContainerFromFile('doctrine_types_default_type');

        self::assertEquals([
            [Gender::class, 'string', 'gender', null],
            [Permissions::class, 'int', 'permissions', null],
        ], $container->getParameter('.elao_enum.doctrine_types'));
    }

    public function testDoctrineTypesEnumSQLDeclaration(): void
    {
        $container = $this->createContainerFromFile('doctrine_types_enum_sql_declaration');

        self::assertEquals([
            [Gender::class, 'string', 'gender', null],
            [AnotherEnum::class, 'enum', 'another', null],
            [Permissions::class, 'int', 'permissions', null],
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
                        'gender' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\GenderType',
                        'another' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\AnotherEnumType',
                        'permissions' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\PermissionsType',
                        'simple_collection_json' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\SimpleEnumJsonCollectionType',
                        'simple_collection_csv' => 'ELAO_ENUM_DT_DBAL\\Elao\\Enum\\Tests\\Fixtures\\Enum\\SimpleEnumCsvCollectionType',
                    ],
                    'mapping_types' => ['enum' => 'string'],
                ],
            ],
        ], $container->getExtensionConfig('doctrine'));
    }

    public function testDoctrineOdmTypesArePrepended(): void
    {
        $container = $this->createContainerFromFile('doctrine_mongodb_types', false);
        /** @var ElaoEnumExtension $ext */
        $ext = $container->getExtension('elao_enum');
        $ext->prepend($container);

        self::assertEquals([
            [
                'types' => [
                    'gender' => 'ELAO_ENUM_DT_ODM\\Elao\\Enum\\Tests\\Fixtures\\Enum\\GenderType',
                    'another' => 'ELAO_ENUM_DT_ODM\\Elao\\Enum\\Tests\\Fixtures\\Enum\\AnotherEnumCollectionType',
                    'permissions' => 'ELAO_ENUM_DT_ODM\\Elao\\Enum\\Tests\\Fixtures\\Enum\\PermissionsType',
                ],
            ],
        ], $container->getExtensionConfig('doctrine_mongodb'));
    }

    public function testTranslationExtractor(): void
    {
        $container = $this->createContainerFromFile('translation_extractor');

        self::assertTrue($container->hasDefinition(EnumExtractor::class), 'translation extractor is loaded');
        self::assertEquals([
            [
                'App\Enum1' => '/var/www/elao/src/Enum1',
                'App\Enum2' => '/var/www/elao/src/Enum2',
            ],
            'messages_test',
            '*Enum.php',
            [
                '/var/www/elao/src/Enum/Ignore1',
                '/var/www/elao/src/Enum/Ignore2',
            ],
        ], $container->getDefinition(EnumExtractor::class)->getArguments());
    }

    public function testJsEnumsExtractor(): void
    {
        $container = $this->createContainerFromFile('js_enums');

        self::assertTrue($container->hasDefinition(DumpJsEnumsCommand::class), 'dump js command is loaded');
        self::assertEquals([
            [
                SimpleEnum::class => 'common/SimpleEnum.js',
                Gender::class => 'common/Gender.js',
                Permissions::class => 'auth/Permissions.js',
            ],
            'assets/js/modules',
            'assets/js/lib',
        ], $container->getDefinition(DumpJsEnumsCommand::class)->getArguments());
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
                'DoctrineMongoDBBundle' => DoctrineMongoDBBundle::class,
            ],
            'kernel.cache_dir' => self::FIXTURES_PATH . '/cache_dir',
        ]));
    }
}
