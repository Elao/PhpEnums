<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\ScalarToEnumTransformer;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class EnumTypeTest extends FormIntegrationTestCase
{
    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "enum_class" is missing.
     */
    public function testThrowExceptionWhenOptionEnumClassIsMissing()
    {
        $this->factory->create(EnumType::class);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "enum_class" with value "Foo" is invalid.
     */
    public function testThrowsExceptionOnInvalidEnumClass()
    {
        $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => \Foo::class]
        );
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Unable to transform value for property path "enum": Expected an array.
     */
    public function testThrowExceptionWhenAppDataNotArrayForMultipleChoices()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'enum_class' => SimpleEnum::class,
            ]
        );

        $field->setData(SimpleEnum::FIRST);
    }

    public function testSubmitSingleNull()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );

        $field->submit(null);

        $this->assertTrue($field->isSynchronized());
        $this->assertNull($field->getData());
        $this->assertSame('', $field->getViewData());
    }

    public function testSubmitSingle()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );

        $field->submit(SimpleEnum::FIRST);

        $this->assertTrue($field->isSynchronized());
        $this->assertSame(SimpleEnum::get(SimpleEnum::FIRST), $field->getData());
        $this->assertSame((string) SimpleEnum::FIRST, $field->getViewData());
    }

    public function testSubmitMultipleNull()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'enum_class' => SimpleEnum::class,
            ]
        );

        $field->submit(null);

        $this->assertSame([], $field->getData());
        $this->assertSame([], $field->getViewData());
    }

    public function testSubmitMultipleExpanded()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'expanded' => true,
                'enum_class' => SimpleEnum::class,
            ]
        );

        $field->submit([SimpleEnum::FIRST]);

        $this->assertTrue($field->isSynchronized());
        $this->assertSame([SimpleEnum::get(SimpleEnum::FIRST)], $field->getData());
        $this->assertSame([SimpleEnum::get(SimpleEnum::FIRST)], $field->getNormData());
        $this->assertTrue($field['1']->getData());
        $this->assertFalse($field['2']->getData());
        $this->assertSame((string) SimpleEnum::FIRST, $field['1']->getViewData());
        $this->assertNull($field['2']->getViewData());
    }

    public function testSetDataSingleNull()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );
        $field->setData(null);
        $this->assertNull($field->getData());
        $this->assertSame('', $field->getViewData());
    }

    public function testSetDataMultipleExpandedNull()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'expanded' => true,
                'enum_class' => SimpleEnum::class,
            ]
        );
        $field->setData(null);
        $this->assertNull($field->getData());
        $this->assertSame([], $field->getViewData());
        foreach ($field->all() as $child) {
            $this->assertSubForm($child, false, null);
        }
    }

    public function testSetDataMultipleNonExpandedNull()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'expanded' => false,
                'enum_class' => SimpleEnum::class,
            ]
        );
        $field->setData(null);
        $this->assertNull($field->getData());
        $this->assertSame([], $field->getViewData());
    }

    public function testSetDataSingle()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );

        $data = SimpleEnum::get(SimpleEnum::FIRST);
        $field->setData($data);

        $this->assertSame($data, $field->getData());
        $this->assertSame((string) SimpleEnum::FIRST, $field->getViewData());
    }

    public function testSetDataMultipleExpanded()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'expanded' => true,
                'enum_class' => SimpleEnum::class,
            ]
        );

        $data = [
            SimpleEnum::get(SimpleEnum::FIRST),
            SimpleEnum::get(SimpleEnum::ZERO),
        ];
        $field->setData($data);

        $this->assertSame($data, $field->getData());
        $this->assertEquals([SimpleEnum::FIRST, SimpleEnum::ZERO], $field->getViewData());
        $this->assertSubForm($field->get('0'), true, (string) SimpleEnum::ZERO);
        $this->assertSubForm($field->get('1'), true, (string) SimpleEnum::FIRST);
        $this->assertSubForm($field->get('2'), false, null);
    }

    public function testSetDataExpanded()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => false,
                'expanded' => true,
                'enum_class' => SimpleEnum::class,
            ]
        );

        $data = SimpleEnum::get(SimpleEnum::FIRST);
        $field->setData($data);

        $this->assertSame($data, $field->getData());
        $this->assertSame(SimpleEnum::get(SimpleEnum::FIRST), $field->getNormData());
        $this->assertSame((string) SimpleEnum::FIRST, $field->getViewData());
        $this->assertSubForm($field->get('0'), false, null);
        $this->assertSubForm($field->get('1'), true, (string) SimpleEnum::FIRST);
        $this->assertSubForm($field->get('2'), false, null);
    }

    public function testSubmitSingleAsValue()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => SimpleEnum::class,
                'as_value' => true,
            ]
        );
        $field->submit(SimpleEnum::FIRST);
        $this->assertTrue($field->isSynchronized());
        $this->assertSame(SimpleEnum::FIRST, $field->getData());
        $this->assertSame((string) SimpleEnum::FIRST, $field->getViewData());
    }

    public function testSubmitMultipleAsValue()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'multiple' => true,
                'expanded' => true,
                'enum_class' => SimpleEnum::class,
                'as_value' => true,
            ]
        );

        $field->submit([SimpleEnum::SECOND, SimpleEnum::FIRST]);

        $this->assertTrue($field->isSynchronized());

        $this->assertSame([SimpleEnum::FIRST, SimpleEnum::SECOND], $field->getData());
        $this->assertSame([SimpleEnum::FIRST, SimpleEnum::SECOND], $field->getNormData());

        $this->assertFalse($field['0']->getData());
        $this->assertTrue($field['2']->getData());
        $this->assertTrue($field['2']->getData());

        $this->assertNull($field['0']->getViewData());
        $this->assertSame((string) SimpleEnum::FIRST, $field['1']->getViewData());
        $this->assertSame((string) SimpleEnum::SECOND, $field['2']->getViewData());
    }

    public function testSubmitReadable()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => Gender::class]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(3, $choices);

        $choice = $choices[0];
        $this->assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        $this->assertSame(Gender::UNKNOW, $choice->value);
        $this->assertSame(Gender::get(Gender::UNKNOW), $choice->data);

        $choice = $choices[1];
        $this->assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        $this->assertSame(Gender::MALE, $choice->value);
        $this->assertSame(Gender::get(Gender::MALE), $choice->data);

        $choice = $choices[2];
        $this->assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        $this->assertSame(Gender::FEMALE, $choice->value);
        $this->assertSame(Gender::get(Gender::FEMALE), $choice->data);

        $field->submit(Gender::MALE);

        $this->assertTrue($field->isSynchronized());
        $this->assertSame(Gender::get(Gender::MALE), $field->getData());
        $this->assertSame(Gender::MALE, $field->getViewData());
    }

    public function testSubmitReadableNull()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => Gender::class]
        );
        $field->submit(null);

        $this->assertTrue($field->isSynchronized());
        $this->assertNull($field->getData());
        $this->assertSame('', $field->getViewData());
    }

    public function testSubmitReadableAsValue()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => Gender::class,
                'as_value' => true,
            ]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(3, $choices);

        $choice = $choices[0];
        $this->assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        $this->assertSame(Gender::UNKNOW, $choice->value);
        $this->assertSame(Gender::UNKNOW, $choice->data);

        $choice = $choices[1];
        $this->assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        $this->assertSame(Gender::MALE, $choice->value);
        $this->assertSame(Gender::MALE, $choice->data);

        $choice = $choices[2];
        $this->assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        $this->assertSame(Gender::FEMALE, $choice->value);
        $this->assertSame(Gender::FEMALE, $choice->data);

        $field->submit(Gender::MALE);
        $this->assertTrue($field->isSynchronized());
        $this->assertSame(Gender::MALE, $field->getData());
        $this->assertSame(Gender::MALE, $field->getViewData());
    }

    public function testChoicesCanBeLimitedUsingChoicesOption()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => Gender::class,
                'choices' => [
                    Gender::get(Gender::MALE),
                    Gender::get(Gender::FEMALE),
                ],
            ]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(2, $choices);

        $choice = $choices[0];
        $this->assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        $this->assertSame(Gender::MALE, $choice->value);
        $this->assertSame(Gender::get(Gender::MALE), $choice->data);

        $choice = $choices[1];
        $this->assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        $this->assertSame(Gender::FEMALE, $choice->value);
        $this->assertSame(Gender::get(Gender::FEMALE), $choice->data);

        $field->submit(Gender::UNKNOW);

        $this->assertFalse($field->isSynchronized());
        $this->assertNull($field->getData());
        $this->assertSame(Gender::UNKNOW, $field->getViewData());
    }

    public function testChoicesAsValueCanBeLimitedUsingChoicesOption()
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => Gender::class,
                'as_value' => true,
                'choices' => [
                    Gender::readableFor(Gender::MALE) => Gender::MALE,
                    Gender::readableFor(Gender::FEMALE) => Gender::FEMALE,
                ],
            ]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(2, $choices);

        $choice = $choices[0];
        $this->assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        $this->assertSame(Gender::MALE, $choice->value);
        $this->assertSame(Gender::MALE, $choice->data);

        $choice = $choices[1];
        $this->assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        $this->assertSame(Gender::FEMALE, $choice->value);
        $this->assertSame(Gender::FEMALE, $choice->data);

        $field->submit(Gender::UNKNOW);

        $this->assertFalse($field->isSynchronized());
        $this->assertNull($field->getData());
        $this->assertSame(Gender::UNKNOW, $field->getViewData());
    }

    public function testTransformationOfScalarChoices()
    {
        $options = interface_exists(ChoiceListInterface::class) ? ['choices_as_values' => true] : [];

        $builder = $this->factory->createBuilder(
            ChoiceType::class,
            Gender::FEMALE(),
            [
                'choices' => ['maleLabel' => Gender::MALE, 'femaleLabel' => Gender::FEMALE],
            ] + $options
        );

        $field = $builder->addModelTransformer(new ScalarToEnumTransformer(Gender::class))->getForm();

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        $this->assertCount(2, $choices);

        $choice = $choices[0];
        $this->assertSame('maleLabel', $choice->label);
        $this->assertSame(Gender::MALE, $choice->value);
        $this->assertSame(Gender::MALE, $choice->data);
        $this->assertSame(Gender::FEMALE(), $field->getData());

        $field->submit(Gender::MALE);

        $this->assertTrue($field->isSynchronized());
        $this->assertSame(Gender::MALE(), $field->getData());
        $this->assertSame(Gender::MALE()->getValue(), $field->getViewData());
    }

    private function assertSubForm(FormInterface $form, $data, $viewData)
    {
        $this->assertSame($data, $form->getData(), '->getData() of sub form #' . $form->getName());
        $this->assertSame($viewData, $form->getViewData(), '->getViewData() of sub form #' . $form->getName());
    }
}
