<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\QueryBodyBackedEnumValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver as SymfonyBackedEnumValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

return static function (ContainerConfigurator $container) {
    // To be dropped when Symfony 5.4 support is dropped
    if (!class_exists(SymfonyBackedEnumValueResolver::class) && interface_exists(ArgumentValueResolverInterface::class)) {
        $container->services()
            ->set(BackedEnumValueResolver::class)->tag('controller.argument_value_resolver', [
                'priority' => 105, // Prior RequestAttributeValueResolver
            ])
        ;
    }

    $container->services()
        ->set(QueryBodyBackedEnumValueResolver::class)->tag('controller.argument_value_resolver', [
            'priority' => 110, // Prior BackedEnumValueResolver
        ])
    ;
};
