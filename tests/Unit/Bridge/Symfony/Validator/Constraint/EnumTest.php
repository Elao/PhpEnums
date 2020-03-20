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
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class EnumTest extends TestCase
{
    public function testDefaultValueIsEnumClass()
    {
        $constraint = new Enum(SimpleEnum::class);

        $this->assertSame(SimpleEnum::class, $constraint->class);
    }

    public function testNoChoicesSetsDefaultCallback()
    {
        $constraint = new Enum(SimpleEnum::class);

        $this->assertSame([SimpleEnum::class, 'instances'], $constraint->callback);
    }

    public function testNoChoicesSetsUserCallback()
    {
        $constraint = new Enum(['class' => SimpleEnum::class, 'callback' => 'allowedValues']);

        $this->assertSame([SimpleEnum::class, 'allowedValues'], $constraint->callback);
    }

    public function testInvalidClassThrowsDefinitionException()
    {
        $this->expectException(ConstraintDefinitionException::class);
        $this->expectExceptionMessage('The "class" option value must be a class FQCN implementing "Elao\Enum\EnumInterface". "Foo" given.');

        new Enum(\Foo::class);
    }

    public function testValidChoiceOption()
    {
        $constraint = new Enum(['class' => SimpleEnum::class, 'choices' => [
            SimpleEnum::FIRST,
            SimpleEnum::SECOND,
        ]]);

        $this->assertNull($constraint->callback);
        $this->assertSame([
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::SECOND),
        ], $constraint->choices);
    }

    public function testValidChoiceOptionAsValues()
    {
        $constraint = new Enum(['class' => SimpleEnum::class, 'asValue' => true, 'choices' => [
            SimpleEnum::FIRST,
            SimpleEnum::SECOND,
        ]]);

        $this->assertNull($constraint->callback);
        $this->assertSame([SimpleEnum::FIRST, SimpleEnum::SECOND], $constraint->choices);
    }

    public function testInvalidChoiceOptionThrowsDefinitionException()
    {
        $this->expectException(ConstraintDefinitionException::class);
        $this->expectExceptionMessage('Choice "bar" is not a valid value for enum type "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum"');

        new Enum(['class' => SimpleEnum::class, 'choices' => ['bar']]);
    }

    public function testDeserializingConstraintRespectsMultitonInstance()
    {
        $constraint = new Enum(['class' => SimpleEnum::class, 'choices' => [
            SimpleEnum::FIRST,
            SimpleEnum::SECOND,
        ]]);

        $constraint = unserialize(serialize($constraint));

        $this->assertSame([
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::SECOND),
        ], $constraint->choices);
    }
}
