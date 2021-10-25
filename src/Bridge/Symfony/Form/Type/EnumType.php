<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\ValueToEnumTransformer;
use Elao\Enum\EnumInterface;
use Elao\Enum\ReadableEnumInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final
 */
class EnumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TL;DR: we usually expect "choices_as_enum_values" and "as_value" to be synchronised,
        // (i.e: expected field data and choices are both raw enumerated value, or both enum instances)
        // but if it doesn't, we need some extra transformations.

        if ($options['choices_as_enum_values'] === $options['as_value']) {
            // No transformation required
            return;
        }

        $transformer = new ValueToEnumTransformer($options['enum_class']);

        $options['as_value']
            // Transform enum instances to values if choices were provided as instances
            // but result data as raw enumerated values were asked:
            ? $builder->addModelTransformer(new ReversedTransformer($transformer))
            // Transform enum values to enum instance if choices were provided as values
            // but result data as raw enumerated values weren't asked:
            : $builder->addModelTransformer($transformer)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    /** @var EnumInterface|ReadableEnumInterface|string $enumClass */
                    $enumClass = $options['enum_class'];

                    if (!$options['choices_as_enum_values']) {
                        return $enumClass::instances();
                    }

                    $possibleValues = $enumClass::values();

                    if (!$this->isReadable($enumClass)) {
                        return $possibleValues;
                    }

                    $choices = [];
                    foreach ($possibleValues as $possibleValue) {
                        $choices[$enumClass::readableFor($possibleValue)] = $possibleValue;
                    }

                    return $choices;
                },
                'choice_label' => function (Options $options) {
                    if ($options['choices_as_enum_values']) {
                        return null;
                    }

                    return $this->isReadable($options['enum_class']) ? 'readable' : 'value';
                },
                'choice_value' => static function (Options $options) {
                    return $options['choices_as_enum_values'] ? null : 'value';
                },
                // If true, will accept and return the enum value instead of object:
                'as_value' => false,
                // If true, overriding the "choices" option will allow using raw enumerated values
                // in provided choice array instead of EnumInterface instances:
                'choices_as_enum_values' => static function (Options $options) {
                    // By default, if result data are expected as raw enumerated values, we expect choices to be too:
                    return $options['as_value'];
                },
            ])
            ->setRequired('enum_class')
            ->setAllowedValues('enum_class', static function ($value) {
                return is_a($value, EnumInterface::class, true);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    private function isReadable(string $enumClass): bool
    {
        return is_a($enumClass, ReadableEnumInterface::class, true);
    }
}
