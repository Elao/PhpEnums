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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

#[Route(path: '/resolver', name: 'from-attributes')]
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

    #[Route(path: '/from-attributes-nullable/{suit}')]
    public function fromAttributesNullable(?Suit $suit = null)
    {
        return new Response($this->getDump($suit));
    }

    #[Route(path: '/from-attributes-with-default')]
    public function fromAttributesWithDefault(Suit $suit = Suit::Spades)
    {
        return new Response($this->getDump($suit));
    }
}
