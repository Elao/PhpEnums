<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Integration\Symfony\TestBundle\Controller;

use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EnumArgumentController extends Controller
{
    public function enum(Gender $gender)
    {
        return new Response($gender->getReadable());
    }

    public function variadicEnum(Gender ...$genders)
    {
        return new Response(implode(',', $genders));
    }
}
