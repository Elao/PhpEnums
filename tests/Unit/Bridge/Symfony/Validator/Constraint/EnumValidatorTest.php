<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Validator\Constraint;

use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

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
        $this->buildViolation('The value you selected is not a valid choice.')
            ->setParameter('{{ value }}', '""')
            ->setCode(Enum::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
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
        $this->buildViolation('The value you selected is not a valid choice.')
            ->setParameter('{{ value }}', '42')
            ->setCode(Enum::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }

    protected function createValidator()
    {
        return new ChoiceValidator();
    }
}
