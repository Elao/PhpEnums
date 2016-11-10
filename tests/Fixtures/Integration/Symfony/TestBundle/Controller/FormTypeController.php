<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Integration\Symfony\TestBundle\Controller;

use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class FormTypeController extends Controller
{
    public function readableEnumFormAction(Request $request)
    {
        $data = [
            'gender' => Gender::get(Gender::MALE),
        ];

        $form = $this->createFormBuilder($data)
            ->add('gender', EnumType::class, ['enum_class' => Gender::class])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        return $this->render('TestBundle::enum_type.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function flaggedEnumFormAction(Request $request)
    {
        $data = [
            'permissions' => Permissions::get(Permissions::READ),
        ];

        $form = $this->createFormBuilder($data)
            ->add('permissions', FlaggedEnumType::class, ['enum_class' => Permissions::class])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        return $this->render('TestBundle::enum_type.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
