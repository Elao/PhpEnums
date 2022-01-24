<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\Type;

use Elao\Enum\ReadableEnumInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType as SymfonyEnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final
 */
class EnumType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            // Override the original type logic for label for readable enums.
            ->setDefault('choice_label', static function (\UnitEnum $choice): string {
                return $choice instanceof ReadableEnumInterface ? $choice->getReadable() : $choice->name;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return SymfonyEnumType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'elao_enum';
    }
}
