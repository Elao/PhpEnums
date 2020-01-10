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
    public function testSetMutipleToFalseThrowsException()
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

    public function testSubmit()
    {
        $field = $this->factory->create(
            FlaggedEnumType::class,
            Permissions::get(Permissions::EXECUTE | Permissions::WRITE),
            ['enum_class' => Permissions::class]
        );

        $view = $field->createView();

        $this->assertEquals([Permissions::EXECUTE, Permissions::WRITE], $view->vars['value']);
        $this->assertSame([
            Permissions::get(Permissions::EXECUTE),
            Permissions::get(Permissions::WRITE),
        ], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(3, $choices);

        $choice = $choices[0];
        $this->assertSame(Permissions::readableFor(Permissions::EXECUTE), $choice->label);
        $this->assertSame((string) Permissions::EXECUTE, $choice->value);
        $this->assertSame(Permissions::get(Permissions::EXECUTE), $choice->data);

        $choice = $choices[1];
        $this->assertSame(Permissions::readableFor(Permissions::WRITE), $choice->label);
        $this->assertSame((string) Permissions::WRITE, $choice->value);
        $this->assertSame(Permissions::get(Permissions::WRITE), $choice->data);

        $choice = $choices[2];
        $this->assertSame(Permissions::readableFor(Permissions::READ), $choice->label);
        $this->assertSame((string) Permissions::READ, $choice->value);
        $this->assertSame(Permissions::get(Permissions::READ), $choice->data);

        $field->submit([Permissions::WRITE, Permissions::READ]);

        $this->assertTrue($field->isSynchronized());
        $this->assertSame(Permissions::get(Permissions::WRITE | Permissions::READ), $field->getData());
        $this->assertEquals([Permissions::WRITE, Permissions::READ], array_values($field->getViewData()));
    }

    public function testSubmitAsValue()
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

        $this->assertEquals([Permissions::EXECUTE, Permissions::WRITE], $view->vars['value']);
        $this->assertSame([Permissions::EXECUTE, Permissions::WRITE], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(3, $choices);

        $choice = $choices[0];
        $this->assertSame(Permissions::readableFor(Permissions::EXECUTE), $choice->label);
        $this->assertSame((string) Permissions::EXECUTE, $choice->value);
        $this->assertSame(Permissions::EXECUTE, $choice->data);

        $choice = $choices[1];
        $this->assertSame(Permissions::readableFor(Permissions::WRITE), $choice->label);
        $this->assertSame((string) Permissions::WRITE, $choice->value);
        $this->assertSame(Permissions::WRITE, $choice->data);

        $choice = $choices[2];
        $this->assertSame(Permissions::readableFor(Permissions::READ), $choice->label);
        $this->assertSame((string) Permissions::READ, $choice->value);
        $this->assertSame(Permissions::READ, $choice->data);

        $field->submit([Permissions::WRITE, Permissions::READ]);

        $this->assertTrue($field->isSynchronized());
        $this->assertSame(Permissions::WRITE | Permissions::READ, $field->getData());
        $this->assertEquals([Permissions::WRITE, Permissions::READ], array_values($field->getViewData()));
    }
}
