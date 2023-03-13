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
use Elao\Enum\Tests\Fixtures\Bridge\Symfony\Validator\Constraint\ObjectWithEnumChoiceAsPhpAttribute;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class EnumTest extends TestCase
{
    /**
     * @dataProvider provide testDefaultValueIsEnumClass data
     */
    public function testDefaultValueIsEnumClass(Enum $constraint, bool $skipped = false): void
    {
        if ($skipped) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }

        self::assertSame(SimpleEnum::class, $constraint->class);
    }

    public function provide testDefaultValueIsEnumClass data(): iterable
    {
        yield 'classic' => [new Enum(SimpleEnum::class)];

        yield 'doctrine style options' => [new Enum(['value' => SimpleEnum::class])];

        if (\PHP_VERSION_ID >= 80000) {
            yield 'with named arguments' => [eval(
            <<<PHP
                use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
                use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

                return new Enum(SimpleEnum::class);
            PHP), !self::isSf52()];
        }
    }

    /**
     * @dataProvider provide testNoChoicesSetsDefaultCallback data
     */
    public function testNoChoicesSetsDefaultCallback(Enum $constraint, bool $skipped = false): void
    {
        if ($skipped) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }
        self::assertSame(SimpleEnum::class, $constraint->class);
        self::assertSame([SimpleEnum::class, 'instances'], $constraint->callback);
    }

    public function provide testNoChoicesSetsDefaultCallback data(): iterable
    {
        yield 'classic' => [new Enum(SimpleEnum::class)];

        yield 'doctrine style options' => [new Enum(['class' => SimpleEnum::class])];

        if (\PHP_VERSION_ID >= 80000) {
            yield 'with named arguments' => [eval(
            <<<PHP
                use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
                use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

                return new Enum(SimpleEnum::class);
            PHP), !self::isSf52()];
        }
    }

    /**
     * @dataProvider provide testNoChoicesSetsUserCallback data
     */
    public function testNoChoicesSetsUserCallback(Enum $constraint, bool $skipped = false): void
    {
        if ($skipped) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }
        self::assertSame(SimpleEnum::class, $constraint->class);
        self::assertSame([SimpleEnum::class, 'allowedValues'], $constraint->callback);
    }

    public function provide testNoChoicesSetsUserCallback data(): iterable
    {
        yield 'doctrine style options' => [new Enum(['class' => SimpleEnum::class, 'callback' => 'allowedValues'])];

        if (\PHP_VERSION_ID >= 80000) {
            yield 'with named arguments' => [eval(
            <<<PHP
                use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
                use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

                return new Enum(class: SimpleEnum::class, callback: 'allowedValues', message: 'foo');
            PHP), !self::isSf52()];
        }
    }

    public function testInvalidClassThrowsDefinitionException(): void
    {
        $this->expectException(ConstraintDefinitionException::class);
        $this->expectExceptionMessage('The "class" option value must be a class FQCN implementing "Elao\Enum\EnumInterface". "Foo" given.');

        new Enum(\Foo::class);
    }

    /**
     * @dataProvider provide testValidChoiceOption data
     */
    public function testValidChoiceOption(Enum $constraint, bool $skipped = false): void
    {
        if ($skipped) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }
        self::assertSame(SimpleEnum::class, $constraint->class);
        self::assertNull($constraint->callback);
        self::assertSame([
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::SECOND),
        ], $constraint->choices);
    }

    public function provide testValidChoiceOption data(): iterable
    {
        yield 'doctrine style options' => [new Enum(['class' => SimpleEnum::class, 'choices' => [
            SimpleEnum::FIRST,
            SimpleEnum::SECOND,
        ]])];

        if (\PHP_VERSION_ID >= 80000) {
            yield 'with named arguments' => [eval(
            <<<PHP
                use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
                use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

                return new Enum(class: SimpleEnum::class, choices: [
                    SimpleEnum::FIRST,
                    SimpleEnum::SECOND,
                ]);
            PHP), !self::isSf52()];
        }
    }

    /**
     * @dataProvider provide testValidChoiceOptionAsValues data
     */
    public function testValidChoiceOptionAsValues(Enum $constraint, bool $skipped = false): void
    {
        if ($skipped) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }
        self::assertSame(SimpleEnum::class, $constraint->class);
        self::assertNull($constraint->callback);
        self::assertSame([SimpleEnum::FIRST, SimpleEnum::SECOND], $constraint->choices);
    }

    public function provide testValidChoiceOptionAsValues data(): iterable
    {
        yield 'doctrine style options' => [new Enum(['class' => SimpleEnum::class, 'asValue' => true, 'choices' => [
            SimpleEnum::FIRST,
            SimpleEnum::SECOND,
        ]])];

        if (\PHP_VERSION_ID >= 80000) {
            yield 'with named arguments' => [eval(
            <<<PHP
                use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
                use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

                return new Enum(class: SimpleEnum::class, asValue: true, choices: [
                    SimpleEnum::FIRST,
                    SimpleEnum::SECOND,
                ]);
            PHP), !self::isSf52()];
        }
    }

    public function testInvalidChoiceOptionThrowsDefinitionException(): void
    {
        $this->expectException(ConstraintDefinitionException::class);
        $this->expectExceptionMessage('Choice "bar" is not a valid value for enum type "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum"');

        new Enum(['class' => SimpleEnum::class, 'choices' => ['bar']]);
    }

    /**
     * @dataProvider provide testDeserializingConstraintRespectsMultitonInstance data
     */
    public function testDeserializingConstraintRespectsMultitonInstance(Enum $constraint, bool $skipped = false): void
    {
        if ($skipped) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }
        $constraint = unserialize(serialize($constraint));

        self::assertSame([
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::SECOND),
        ], $constraint->choices);
    }

    public function provide testDeserializingConstraintRespectsMultitonInstance data(): iterable
    {
        yield 'doctrine style options' => [new Enum(['class' => SimpleEnum::class, 'choices' => [
            SimpleEnum::FIRST,
            SimpleEnum::SECOND,
        ]])];

        if (\PHP_VERSION_ID >= 80000) {
            yield 'with named arguments' => [eval(
            <<<PHP
                use Elao\Enum\Bridge\Symfony\Validator\Constraint\Enum;
                use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;

                return new Enum(class: SimpleEnum::class, choices: [
                    SimpleEnum::FIRST,
                    SimpleEnum::SECOND,
                ]);
            PHP), !self::isSf52()];
        }
    }

    /**
     * @requires PHP 8
     */
    public function testPhp8AttributeIsLoaded(): void
    {
        if (!self::isSf52()) {
            self::markTestSkipped('Requires Symfony Validator Component 5.2+');
        }

        $loader = new AnnotationLoader();

        $metadata = new ClassMetadata(ObjectWithEnumChoiceAsPhpAttribute::class);

        $loader->loadClassMetadata($metadata);

        $expected = new ClassMetadata(ObjectWithEnumChoiceAsPhpAttribute::class);

        $expected->addPropertyConstraint('simple', new Enum(SimpleEnum::class));
        $expected->addPropertyConstraint('simple', new NotNull());
        $expected->addPropertyConstraint('restrictedChoices', new Enum(SimpleEnum::class, [
            SimpleEnum::ZERO,
            SimpleEnum::FIRST,
        ]));
        $expected->addPropertyConstraint('restrictedChoices', new NotNull());

        // load reflection class so that the comparison passes
        $expected->getReflectionClass();

        self::assertEquals($expected, $metadata);
    }

    private static function isSf52(): bool
    {
        static $sf52;

        if (null === $sf52) {
            // Sf < 5.2 Choice constructor has only the `$options` argument.
            $sf52 = (new \ReflectionClass(Choice::class))->getConstructor()->getNumberOfParameters() > 1;
        }

        return $sf52;
    }
}
