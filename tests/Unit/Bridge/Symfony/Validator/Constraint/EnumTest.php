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

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultValueIsEnumClass()
    {
        $constraint = new Enum(SimpleEnum::class);

        $this->assertSame(SimpleEnum::class, $constraint->class);
    }

    public function testNoChoicesSetsCallback()
    {
        $constraint = new Enum(SimpleEnum::class);

        $this->assertSame([SimpleEnum::class, 'instances'], $constraint->callback);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage The "class" option value must be a class FQCN implementing "Elao\Enum\EnumInterface". "Foo" given.
     */
    public function testInvalidClassThrowsDefinitionException()
    {
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

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @expectedExceptionMessage Choice "bar" is not a valid value for enum type "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum"
     */
    public function testInvalidChoiceOptionThrowsDefinitionException()
    {
        new Enum(['class' => SimpleEnum::class, 'choices' => ['bar']]);
    }
}
