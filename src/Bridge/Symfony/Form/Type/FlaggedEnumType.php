<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\BitmaskToBitFlagsValueTransformer;
use Elao\Enum\Bridge\Symfony\Form\DataTransformer\SingleToCollectionFlagEnumTransformer;
use Elao\Enum\FlaggedEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final
 */
class FlaggedEnumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['multiple']) {
            throw new InvalidConfigurationException(sprintf(
                'The "multiple" option of the "%s" form type cannot be set to false.',
                static::class
            ));
        }

        $transformer = $options['as_value']
            ? new BitmaskToBitFlagsValueTransformer($options['enum_class'])
            : new SingleToCollectionFlagEnumTransformer($options['enum_class'])
        ;

        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('multiple', true)
            ->setAllowedValues('enum_class', static function ($value) {
                return is_a($value, FlaggedEnum::class, true);
            })
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
