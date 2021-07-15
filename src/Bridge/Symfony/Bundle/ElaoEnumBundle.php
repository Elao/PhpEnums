<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Bundle;

use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Compiler\DoctrineDBALTypesPass;
use Elao\Enum\Bridge\Symfony\Bundle\DependencyInjection\Compiler\DoctrineODMTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ElaoEnumBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new DoctrineDBALTypesPass($container->getParameter('kernel.cache_dir') . '/elao_enum_doctrine_dbal_types.php')
        );
        $container->addCompilerPass(
            new DoctrineODMTypesPass($container->getParameter('kernel.cache_dir') . '/elao_enum_doctrine_odm_types.php')
        );
    }
}
