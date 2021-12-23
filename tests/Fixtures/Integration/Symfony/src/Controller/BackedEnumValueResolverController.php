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
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver\ResolveBackedEnumValue;
use Elao\Enum\Bridge\Symfony\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver\ResolveFrom;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

#[Route(path: '/resolver')]
class BackedEnumValueResolverController extends AbstractController
{
    use VarDumperTestTrait;

    public function __construct()
    {
        $this->setUpVarDumper([], CliDumper::DUMP_LIGHT_ARRAY);
    }

    #[Route(path: '/from-attributes/{suit}')]
    public function fromAttributes(Suit $suit)
    {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query')]
    public function fromQuery(
        #[ResolveBackedEnumValue(ResolveFrom::Query)]
        Suit $suit
    ) {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-nullable')]
    public function fromQueryNullable(
        #[ResolveBackedEnumValue(ResolveFrom::Query)]
        ?Suit $suit
    ) {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-with-default')]
    public function fromQueryWithDefault(
        #[ResolveBackedEnumValue(ResolveFrom::Query)]
        ?Suit $suit = Suit::Hearts
    ) {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-with-default-non-nullable')]
    public function fromQueryWithDefaultNonNullable(
        #[ResolveBackedEnumValue(ResolveFrom::Query)]
        Suit $suit = Suit::Hearts
    ) {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-query-variadics')]
    public function fromQueryVariadics(
        #[ResolveBackedEnumValue(ResolveFrom::Query)]
        Suit ...$suit
    ) {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-body', methods: 'POST')]
    public function fromBody(
        #[ResolveBackedEnumValue(ResolveFrom::Body)]
        Suit $suit
    ) {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-body-variadics', methods: 'POST')]
    public function fromBodyVariadics(
        #[ResolveBackedEnumValue(ResolveFrom::Body)]
        Suit ...$suit
    ) {
        return new Response($this->getDump($suit));
    }
}
