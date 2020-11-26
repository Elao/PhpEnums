<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class FlaggedEnumTypeTest extends FormIntegrationTestCase
{
    public function testSetMutipleToFalseThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The "multiple" option of the "Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType" form type cannot be set to false.');

        $this->factory->create(
            FlaggedEnumType::class,
            null,
            [
                'enum_class' => Permissions::class,
                'multiple' => false,
            ]
        );
    }

    public function testSubmit(): void
    {
        $field = $this->factory->create(
            FlaggedEnumType::class,
            Permissions::get(Permissions::EXECUTE | Permissions::WRITE),
            ['enum_class' => Permissions::class]
        );

        $view = $field->createView();

        self::assertEquals([Permissions::EXECUTE, Permissions::WRITE], $view->vars['value']);
        self::assertSame([
            Permissions::get(Permissions::EXECUTE),
            Permissions::get(Permissions::WRITE),
        ], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Permissions::readableFor(Permissions::EXECUTE), $choice->label);
        self::assertSame((string) Permissions::EXECUTE, $choice->value);
        self::assertSame(Permissions::get(Permissions::EXECUTE), $choice->data);

        $choice = $choices[1];
        self::assertSame(Permissions::readableFor(Permissions::WRITE), $choice->label);
        self::assertSame((string) Permissions::WRITE, $choice->value);
        self::assertSame(Permissions::get(Permissions::WRITE), $choice->data);

        $choice = $choices[2];
        self::assertSame(Permissions::readableFor(Permissions::READ), $choice->label);
        self::assertSame((string) Permissions::READ, $choice->value);
        self::assertSame(Permissions::get(Permissions::READ), $choice->data);

        $field->submit([Permissions::WRITE, Permissions::READ]);

        self::assertTrue($field->isSynchronized());
        self::assertSame(Permissions::get(Permissions::WRITE | Permissions::READ), $field->getData());
        self::assertEquals([Permissions::WRITE, Permissions::READ], array_values($field->getViewData()));
    }

    public function testSubmitAsValue(): void
    {
        $field = $this->factory->create(
            FlaggedEnumType::class,
            Permissions::EXECUTE | Permissions::WRITE,
            [
                'enum_class' => Permissions::class,
                'as_value' => true,
            ]
        );

        $view = $field->createView();

        self::assertEquals([Permissions::EXECUTE, Permissions::WRITE], $view->vars['value']);
        self::assertSame([Permissions::EXECUTE, Permissions::WRITE], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Permissions::readableFor(Permissions::EXECUTE), $choice->label);
        self::assertSame((string) Permissions::EXECUTE, $choice->value);
        self::assertSame(Permissions::EXECUTE, $choice->data);

        $choice = $choices[1];
        self::assertSame(Permissions::readableFor(Permissions::WRITE), $choice->label);
        self::assertSame((string) Permissions::WRITE, $choice->value);
        self::assertSame(Permissions::WRITE, $choice->data);

        $choice = $choices[2];
        self::assertSame(Permissions::readableFor(Permissions::READ), $choice->label);
        self::assertSame((string) Permissions::READ, $choice->value);
        self::assertSame(Permissions::READ, $choice->data);

        $field->submit([Permissions::WRITE, Permissions::READ]);

        self::assertTrue($field->isSynchronized());
        self::assertSame(Permissions::WRITE | Permissions::READ, $field->getData());
        self::assertEquals([Permissions::WRITE, Permissions::READ], array_values($field->getViewData()));
    }
}
