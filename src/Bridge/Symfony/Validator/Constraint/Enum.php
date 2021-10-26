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

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
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

    public function __construct(
        $class = null,
        ?array $choices = null,
        ?bool $asValue = null,
        $callback = null,
        ?bool $multiple = null,
        ?bool $strict = null,
        ?int $min = null,
        ?int $max = null,
        ?string $message = null,
        ?string $multipleMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        $groups = null,
        $payload = null
    ) {
        parent::__construct(
            $class, // "class" is the default option here and supersedes "choices" from parent class.
            $callback,
            $multiple,
            $strict,
            $min,
            $max,
            $message,
            $multipleMessage,
            $minMessage,
            $maxMessage,
            $groups,
            $payload
        );

        $this->asValue = $asValue ?? $this->asValue;
        // Choices are either provided as $class argument when used as "options" (Doctrine annotations style) and handled in parent class,
        // or provided with positional or named argument using PHP construct:
        $this->choices = $choices ?? $this->choices;

        if (!is_a($this->class, EnumInterface::class, true)) {
            throw new ConstraintDefinitionException(sprintf(
                'The "class" option value must be a class FQCN implementing "%s". "%s" given.',
                EnumInterface::class,
                $this->class
            ));
        }

        // Normalize choices
        if (\is_array($this->choices)) {
            $choices = [];
            foreach ($this->choices as $choiceValue) {
                if (false === \call_user_func([$this->class, 'accepts'], $choiceValue)) {
                    throw new ConstraintDefinitionException(sprintf(
                        'Choice %s is not a valid value for enum type "%s".',
                        json_encode($choiceValue),
                        $this->class
                    ));
                }

                $choices[] = $this->asValue ? $choiceValue : \call_user_func([$this->class, 'get'], $choiceValue);
            }

            $this->choices = $choices;
        }

        // only set the callback if no choice list set
        if (!\is_array($this->choices)) {
            // Check for a user provided callback first
            if ($this->callback) {
                $this->callback = [$this->class, $this->callback];
            } else {
                $this->callback = $this->asValue ? [$this->class, 'values'] : [$this->class, 'instances'];
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function validatedBy()
    {
        return ChoiceValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption(): ?string
    {
        return 'class';
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getRequiredOptions()
    {
        return ['class'];
    }

    /**
     * Fixup deserialized enum instances by replacing them with actual multiton instances,
     * so strict comparison works.
     */
    public function __wakeup()
    {
        if (!$this->asValue && \is_array($this->choices)) {
            $this->choices = array_map(static function (EnumInterface $enum): EnumInterface {
                /** @var string|EnumInterface $enumClass */
                $enumClass = \get_class($enum);

                return $enumClass::get($enum->getValue());
            }, $this->choices);
        }
    }
}
