<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace App\Controller;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\ValueToEnumTransformer;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class FormTypeController extends AbstractController
{
    public function readableEnumForm(Request $request)
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

        return $this->render('@tests/enum_type.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function flaggedEnumForm(Request $request)
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

        return $this->render('@tests/enum_type.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function valueToEnumTransformerChoiceForm(Request $request)
    {
        $data = [
            'gender' => Gender::get(Gender::MALE),
            'simpleEnum' => SimpleEnum::get(SimpleEnum::SECOND),
        ];

        $builder = $this->createFormBuilder($data)
            ->add('gender', ChoiceType::class, [
                'choices' => ['customMaleLabel' => Gender::MALE, 'customFemaleLabel' => Gender::FEMALE],
            ])
            ->add('simpleEnum', ChoiceType::class, [
                'choices' => ['customOneLabel' => SimpleEnum::FIRST, 'customSecondLabel' => SimpleEnum::SECOND],
            ])
            ->add('submit', SubmitType::class)
        ;

        $builder->get('gender')->addModelTransformer(new ValueToEnumTransformer(Gender::class));
        $builder->get('simpleEnum')->addModelTransformer(new ValueToEnumTransformer(SimpleEnum::class));

        $form = $builder->getForm();

        $form->handleRequest($request);

        return $this->render('@tests/enum_type.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function choicesAsEnumValuesEnumForm(Request $request)
    {
        $data = [
            'gender' => Gender::get(Gender::MALE),
            'simpleEnum' => SimpleEnum::get(SimpleEnum::SECOND),
        ];

        $builder = $this->createFormBuilder($data)
            ->add('gender', EnumType::class, [
                'enum_class' => Gender::class,
                'choices' => ['customMaleLabel' => Gender::MALE, 'customFemaleLabel' => Gender::FEMALE],
                'choices_as_enum_values' => true,
            ])
            ->add('simpleEnum', EnumType::class, [
                'enum_class' => SimpleEnum::class,
                'choices' => ['customOneLabel' => SimpleEnum::FIRST, 'customSecondLabel' => SimpleEnum::SECOND],
                'choices_as_enum_values' => true,
            ])
            ->add('submit', SubmitType::class)
        ;

        $form = $builder->getForm();

        $form->handleRequest($request);

        return $this->render('@tests/enum_type.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
