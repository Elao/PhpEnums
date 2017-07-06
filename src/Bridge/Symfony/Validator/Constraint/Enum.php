<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Bridge\Symfony\Validator\Constraint;

use Elao\Enum\EnumInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class Enum extends Choice
{
    /** @var string */
    public $class;

    /**
     * Set to true in order to validate enum values instead of instances.
     *
     * @var bool
     */
    public $asValue = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->strict = true;

        if (!is_a($this->class, EnumInterface::class, true)) {
            throw new ConstraintDefinitionException(sprintf(
                'The "class" option value must be a class FQCN implementing "%s". "%s" given.',
                EnumInterface::class,
                $this->class
            ));
        }

        // Normalize choices
        if (is_array($this->choices)) {
            $choices = [];
            foreach ($this->choices as $choiceValue) {
                if (false === call_user_func([$this->class, 'accepts'], $choiceValue)) {
                    throw new ConstraintDefinitionException(sprintf(
                        'Choice %s is not a valid value for enum type "%s".',
                        json_encode($choiceValue),
                        $this->class
                    ));
                }

                $choices[] = $this->asValue ? $choiceValue : call_user_func([$this->class, 'get'], $choiceValue);
            }

            $this->choices = $choices;
        }

        // only set the callback if no choice list set
        if (!is_array($this->choices)) {
            $this->callback = $this->asValue ? [$this->class, 'values'] : [$this->class, 'instances'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return ChoiceValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'class';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['class'];
    }
}
