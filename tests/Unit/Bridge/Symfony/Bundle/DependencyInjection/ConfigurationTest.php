<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Bundle\DependencyInjection;

use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Configuration;
use Elao\Enum\Tests\Fixtures\Enum\AnotherEnum;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\Fixtures\Enum\YetAnotherEnum;
use Elao\Enum\Tests\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    use ExpectDeprecationTrait;

    public function testDefaultConfig(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[]]);

        self::assertEquals($this->getDefaultConfig(), $config);
    }

    public function testDisabledConfig(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'argument_value_resolver' => false,
            'serializer' => false,
            'translation_extractor' => false,
        ]]);

        self::assertEquals(array_merge($this->getDefaultConfig(), [
            'argument_value_resolver' => ['enabled' => false],
            'serializer' => ['enabled' => false],
        ]), $config);
    }

    public function testDoctrineConfig(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'doctrine' => [
                'enum_sql_declaration' => true,
                'types' => [
                     'gender' => ['class' => Gender::class, 'type' => 'string'],
                     'another' => ['class' => AnotherEnum::class, 'type' => 'enum'],
                     'permissions' => ['class' => Permissions::class, 'type' => 'int'],
                     'simple_collection_json' => ['class' => SimpleEnum::class, 'type' => 'json_collection'],
                     'simple_collection_csv' => ['class' => SimpleEnum::class, 'type' => 'csv_collection'],
                ],
            ],
        ]]);

        self::assertEquals([
            'doctrine' => [
                'enum_sql_declaration' => true,
                'types' => [
                    'gender' => ['class' => Gender::class, 'type' => 'string', 'default' => null],
                    'another' => ['class' => AnotherEnum::class, 'type' => 'enum', 'default' => null],
                    'permissions' => ['class' => Permissions::class, 'type' => 'int', 'default' => null],
                    'simple_collection_json' => ['class' => SimpleEnum::class, 'type' => 'json_collection', 'default' => null],
                    'simple_collection_csv' => ['class' => SimpleEnum::class, 'type' => 'csv_collection', 'default' => null],
                ],
            ],
        ] + $this->getDefaultConfig(), $config);
    }

    /**
     * @group legacy
     */
    public function testDeprecatedDoctrineConfig(): void
    {
        $this->expectDeprecation('Using enum FQCN as keys at path "elao_enum.doctrine.types" is deprecated. Provide the name as keys and add the "class" option for each entry instead.');

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'doctrine' => [
                'enum_sql_declaration' => true,
                'types' => [
                    YetAnotherEnum::class => YetAnotherEnum::class,
                    Gender::class => ['name' => 'gender', 'type' => 'string'],
                    AnotherEnum::class => ['name' => 'another', 'type' => 'enum'],
                    Permissions::class => ['name' => 'permissions', 'type' => 'int'],
                ],
            ],
        ]]);

        self::assertEquals([
            'doctrine' => [
                'enum_sql_declaration' => true,
                'types' => [
                    YetAnotherEnum::class => ['class' => YetAnotherEnum::class, 'type' => null, 'default' => null],
                    'gender' => ['class' => Gender::class, 'type' => 'string', 'default' => null],
                    'another' => ['class' => AnotherEnum::class, 'type' => 'enum', 'default' => null],
                    'permissions' => ['class' => Permissions::class, 'type' => 'int', 'default' => null],
                ],
            ],
        ] + $this->getDefaultConfig(), $config);
    }

    public function testFQCNAsKeyValuesAllowed(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'doctrine' => [
                'enum_sql_declaration' => true,
                'types' => [
                    Gender::class => Gender::class,
                    AnotherEnum::class => AnotherEnum::class,
                ],
            ],
        ]]);

        self::assertEquals([
            'doctrine' => [
                'enum_sql_declaration' => true,
                'types' => [
                    Gender::class => ['class' => Gender::class, 'type' => null, 'default' => null],
                    AnotherEnum::class => ['class' => AnotherEnum::class, 'type' => null, 'default' => null],
                ],
            ],
        ] + $this->getDefaultConfig(), $config);
    }

    public function testDoctrineTypeConfigWithInvalidEnumClass(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid configuration for path "elao_enum.doctrine.types.std.class": Invalid class. Expected instance of "Elao\Enum\EnumInterface". Got "stdClass".');

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [[
            'doctrine' => [
                'types' => [
                    'std' => ['class' => \stdClass::class],
                ],
            ],
        ]]);
    }

    public function testTranslationExtractorConfig(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'translation_extractor' => [
                'enabled' => true,
                'paths' => ['App\Enum' => '%kernel.project_dir%/src/Enum'],
                'domain' => 'messages_test',
                'filename_pattern' => '*Enum.php',
                'ignore' => ['%kernel.project_dir%/src/Enum/Ignored'],
            ],
        ]]);

        self::assertEquals([
            'enabled' => true,
            'paths' => ['App\Enum' => '%kernel.project_dir%/src/Enum'],
            'domain' => 'messages_test',
            'filename_pattern' => '*Enum.php',
            'ignore' => ['%kernel.project_dir%/src/Enum/Ignored'],
        ], $config['translation_extractor']);
    }

    public function testJsEnumConfig(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'js' => [
                'lib_path' => 'assets/lib',
                'base_dir' => 'assets/modules',
                'paths' => [
                    Gender::class => 'common/Gender.js',
                    Permissions::class => 'auth/Permissions.js',
                ],
            ],
        ]]);

        self::assertEquals([
            'lib_path' => 'assets/lib',
            'base_dir' => 'assets/modules',
            'paths' => [
                Gender::class => 'common/Gender.js',
                Permissions::class => 'auth/Permissions.js',
            ],
        ], $config['js']);
    }

    private function getDefaultConfig(): array
    {
        return [
            'argument_value_resolver' => ['enabled' => true],
            'serializer' => ['enabled' => true],
            'translation_extractor' => [
                'enabled' => false,
                'paths' => [],
                'domain' => 'messages',
                'filename_pattern' => '*.php',
                'ignore' => [],
            ],
            'doctrine' => [
                'enum_sql_declaration' => false,
                'types' => [],
            ],
            'doctrine_mongodb' => [
                'types' => [],
            ],
            'js' => [
                'base_dir' => null,
                'lib_path' => null,
                'paths' => [],
            ],
        ];
    }
}
