<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class FlaggedEnumTypeTest extends FormIntegrationTestCase
{
    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The "multiple" option of the "Elao\Enum\Bridge\Symfony\Form\Type\FlaggedEnumType" form type cannot be set to false.
     */
    public function testSetMutipleToFalseThrowsException()
    {
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
            Permissions::create(Permissions::EXECUTE | Permissions::WRITE),
            ['enum_class' => Permissions::class]
        );

        $view = $field->createView();

        $this->assertEquals([Permissions::EXECUTE, Permissions::WRITE], $view->vars['value']);
        $this->assertEquals([
            Permissions::create(Permissions::EXECUTE),
            Permissions::create(Permissions::WRITE),
        ], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(3, $choices);

        $choice = $choices[0];
        $this->assertSame(Permissions::readableFor(Permissions::EXECUTE), $choice->label);
        $this->assertEquals(Permissions::EXECUTE, $choice->value);
        $this->assertEquals(Permissions::create(Permissions::EXECUTE), $choice->data);

        $choice = $choices[1];
        $this->assertSame(Permissions::readableFor(Permissions::WRITE), $choice->label);
        $this->assertEquals(Permissions::WRITE, $choice->value);
        $this->assertEquals(Permissions::create(Permissions::WRITE), $choice->data);

        $choice = $choices[2];
        $this->assertSame(Permissions::readableFor(Permissions::READ), $choice->label);
        $this->assertEquals(Permissions::READ, $choice->value);
        $this->assertEquals(Permissions::create(Permissions::READ), $choice->data);

        $field->submit([Permissions::WRITE, Permissions::READ]);

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(Permissions::create(Permissions::WRITE | Permissions::READ), $field->getData());
        $this->assertEquals([Permissions::WRITE, Permissions::READ], $field->getViewData());
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
        $this->assertEquals([Permissions::EXECUTE, Permissions::WRITE], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(3, $choices);

        $choice = $choices[0];
        $this->assertSame(Permissions::readableFor(Permissions::EXECUTE), $choice->label);
        $this->assertEquals(Permissions::EXECUTE, $choice->value);
        $this->assertEquals(Permissions::EXECUTE, $choice->data);

        $choice = $choices[1];
        $this->assertSame(Permissions::readableFor(Permissions::WRITE), $choice->label);
        $this->assertEquals(Permissions::WRITE, $choice->value);
        $this->assertEquals(Permissions::WRITE, $choice->data);

        $choice = $choices[2];
        $this->assertSame(Permissions::readableFor(Permissions::READ), $choice->label);
        $this->assertEquals(Permissions::READ, $choice->value);
        $this->assertEquals(Permissions::READ, $choice->data);

        $field->submit([Permissions::WRITE, Permissions::READ]);

        $this->assertTrue($field->isSynchronized());
        $this->assertEquals(Permissions::WRITE | Permissions::READ, $field->getData());
        $this->assertEquals([Permissions::WRITE, Permissions::READ], array_values($field->getViewData()));
    }
}
