<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Validator\Constraint;

use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Constraints\JsonValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class EnumValidatorTest extends ConstraintValidatorTestCase
{
    public function testNullIsValid()
    {
        $this->validator->validate(null, new Enum(Gender::class));
        $this->assertNoViolation();
    }

    public function testBlankStringIsInvalid()
    {
        $this->validator->validate('', new Enum(Gender::class));
        $violation = $this->buildViolation('The value you selected is not a valid choice.')
            ->setParameter('{{ value }}', '""')
            ->setCode(Enum::NO_SUCH_CHOICE_ERROR)
        ;

        $this->is4_3_OrUpper() && $violation->setParameter('{{ choices }}', 'object, object, object');

        $violation->assertRaised();
    }

    public function testValid()
    {
        foreach (Gender::instances() as $value) {
            $this->validator->validate($value, new Enum(Gender::class));
        }

        $this->assertNoViolation();
    }

    public function testValidMultiple()
    {
        $this->validator->validate([
            Gender::get(Gender::MALE),
            Gender::get(Gender::FEMALE),
        ], new Enum([
            'class' => Gender::class,
            'multiple' => true,
        ]));

        $this->assertNoViolation();
    }

    public function testValidValues()
    {
        foreach (Gender::values() as $value) {
            $this->validator->validate($value, new Enum([
                'class' => Gender::class,
                'asValue' => true,
            ]));
        }

        $this->assertNoViolation();
    }

    public function testInvalidValue()
    {
        $this->validator->validate(42, new Enum(Gender::class));
        $violation = $this->buildViolation('The value you selected is not a valid choice.')
            ->setParameter('{{ value }}', '42')
            ->setCode(Enum::NO_SUCH_CHOICE_ERROR)
        ;

        $this->is4_3_OrUpper() && $violation->setParameter('{{ choices }}', 'object, object, object');

        $violation->assertRaised();
    }

    protected function createValidator()
    {
        return new ChoiceValidator();
    }

    private function is4_3_OrUpper(): bool
    {
        // JsonValidator was added in 4.3
        return class_exists(JsonValidator::class);
    }
}
