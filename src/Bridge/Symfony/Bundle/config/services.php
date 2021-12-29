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

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(BackedEnumValueResolver::class)->tag('controller.argument_value_resolver', [
            'priority' => 105, // Prior RequestAttributeValueResolver
        ])
    ;
    $container->services()
        ->set(QueryBodyBackedEnumValueResolver::class)->tag('controller.argument_value_resolver', [
            'priority' => 110, // Prior BackedEnumValueResolver
        ])
    ;
};
