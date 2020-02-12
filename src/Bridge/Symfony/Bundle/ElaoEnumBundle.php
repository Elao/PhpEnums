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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ElaoEnumBundle extends Bundle
{
    const DOCTRINE_TYPES_FILENAME = 'elao_enum_doctrine_types.php';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineDBALTypesPass($container->getParameter('kernel.cache_dir') . '/' . self::DOCTRINE_TYPES_FILENAME));
    }
}
