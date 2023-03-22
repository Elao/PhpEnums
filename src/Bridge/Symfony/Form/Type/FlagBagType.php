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

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\FlagBagToCollectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final
 */
class FlagBagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['multiple']) {
            throw new InvalidConfigurationException(sprintf(
                'The "multiple" option of the "%s" form type cannot be set to false.',
                static::class
            ));
        }

        if (!\is_string($options['class']) || !is_subclass_of($options['class'], \BackedEnum::class)) {
            throw new InvalidConfigurationException(sprintf(
                'The "class" option of the "%s" form type must contains the FQCN of a BackedEnum.',
                static::class
            ));
        }

        $builder->addModelTransformer(new FlagBagToCollectionTransformer($options['class']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('multiple', true)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return EnumType::class;
    }
}
