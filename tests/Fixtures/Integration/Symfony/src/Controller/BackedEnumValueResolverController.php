<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Controller;

use App\Enum\Suit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

if (!class_exists(Route::class)) {
    class_alias(\Symfony\Component\Routing\Annotation\Route::class, Route::class);
}

#[Route(path: '/resolver', name: 'from-attributes')]
class BackedEnumValueResolverController extends AbstractController
{
    use VarDumperTestTrait;

    public function __construct()
    {
        $this->setUpVarDumper([], CliDumper::DUMP_LIGHT_ARRAY);
    }

    #[Route(path: '/from-attributes/{suit}')]
    public function fromAttributes(Suit $suit): Response
    {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-attributes-nullable/{suit}')]
    public function fromAttributesNullable(?Suit $suit = null): Response
    {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-attributes-with-default')]
    public function fromAttributesWithDefault(Suit $suit = Suit::Spades): Response
    {
        return new Response($this->getDump($suit));
    }
}
