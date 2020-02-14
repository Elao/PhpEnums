<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Compiler;

use Elao\Enum\Bridge\Doctrine\DBAL\Types\TypesDumper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineDBALTypesPass implements CompilerPassInterface
{
    /** @var string */
    private $typesFilePath;

    public function __construct(string $typesFilePath)
    {
        $this->typesFilePath = $typesFilePath;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('.elao_enum.doctrine_types')) {
            return;
        }

        $types = $container->getParameter('.elao_enum.doctrine_types');

        (new TypesDumper())->dumpToFile($this->typesFilePath, $types);

        $container->getDefinition('doctrine.dbal.connection_factory')->setFile($this->typesFilePath);
    }
}
