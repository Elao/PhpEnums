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
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromBody;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\Attributes\BackedEnumFromQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

if (!class_exists(Route::class)) {
    class_alias(\Symfony\Component\Routing\Annotation\Route::class, Route::class);
}

#[Route(path: '/resolver', name: 'from-query-body')]
class QueryBodyBackedEnumValueResolverController extends AbstractController
{
    use VarDumperTestTrait;

    public function __construct()
    {
        $this->setUpVarDumper([], CliDumper::DUMP_LIGHT_ARRAY);
    }

    #[Route(path: '/from-query')]
    public function fromQuery(
        #[BackedEnumFromQuery]
        Suit $suit
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-nullable')]
    public function fromQueryNullable(
        #[BackedEnumFromQuery]
        ?Suit $suit
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-with-default')]
    public function fromQueryWithDefault(
        #[BackedEnumFromQuery]
        ?Suit $suit = Suit::Hearts
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-with-default-non-nullable')]
    public function fromQueryWithDefaultNonNullable(
        #[BackedEnumFromQuery]
        Suit $suit = Suit::Hearts
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-variadics')]
    public function fromQueryVariadics(
        #[BackedEnumFromQuery]
        Suit ...$suit
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-body', methods: 'POST')]
    public function fromBody(
        #[BackedEnumFromBody]
        Suit $suit
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-body-variadics', methods: 'POST')]
    public function fromBodyVariadics(
        #[BackedEnumFromBody]
        Suit ...$suit
    ): Response {
        return new Response($this->getDump($suit));
    }
}
