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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[]]);

        $this->assertEquals([
            'argument_value_resolver' => ['enabled' => true],
            'serializer' => ['enabled' => true],
        ], $config);
    }

    public function testDisabledConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[
            'argument_value_resolver' => false,
            'serializer' => false,
        ]]);

        $this->assertEquals([
            'argument_value_resolver' => ['enabled' => false],
            'serializer' => ['enabled' => false],
        ], $config);
    }
}
