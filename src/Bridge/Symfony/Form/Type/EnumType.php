<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Form\Type;

use Elao\Enum\EnumInterface;
use Elao\Enum\ReadableEnumInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnumType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $enumClass = $options['enum_class'];

                    if (!$options['as_value']) {
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
                    if ($options['as_value']) {
                        return null;
                    }

                    return $this->isReadable($options['enum_class']) ? 'readable' : 'value';
                },
                'choice_value' => function (Options $options) {
                    return $options['as_value'] ? null : 'value';
                },
                // If true, will accept and return the enum value instead of object:
                'as_value' => false,
            ])
            ->setRequired('enum_class')
            ->setAllowedValues('enum_class', function ($value) {
                return is_a($value, EnumInterface::class, true);
            })
        ;

        // To be removed once Symfony 2.8 is not LTS anymore
        if (self::shouldUseChoicesAsValues()) {
            $resolver->setDefault('choices_as_values', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    private function isReadable(string $enumClass): bool
    {
        return is_a($enumClass, ReadableEnumInterface::class, true);
    }

    /**
     * Returns true if the 2.8 Form component is being used by the application.
     */
    private static function shouldUseChoicesAsValues(): bool
    {
        return true === interface_exists(ChoiceListInterface::class);
    }
}
