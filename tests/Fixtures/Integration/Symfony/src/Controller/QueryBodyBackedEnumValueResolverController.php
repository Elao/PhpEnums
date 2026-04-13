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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

#[Route(path: '/resolver', name: 'from-body')]
class QueryBodyBackedEnumValueResolverController extends AbstractController
{
    use VarDumperTestTrait;

    public function __construct()
    {
        $this->setUpVarDumper([], CliDumper::DUMP_LIGHT_ARRAY);
    }

    #[Route(path: '/from-body', methods: 'POST')]
    public function fromBody(
        #[BackedEnumFromBody]
        Suit $suit,
    ): Response {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-body-variadics', methods: 'POST')]
    public function fromBodyVariadics(
        #[BackedEnumFromBody]
        Suit ...$suit,
    ): Response {
        return new Response($this->getDump($suit));
    }
}
