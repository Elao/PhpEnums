<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\ValueToEnumTransformer;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class EnumTypeTest extends FormIntegrationTestCase
{
    public function testThrowExceptionWhenOptionEnumClassIsMissing(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "enum_class" is missing.');

        $this->factory->create(EnumType::class);
    }

    public function testThrowsExceptionOnInvalidEnumClass(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "enum_class" with value "Foo" is invalid.');

        $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => \Foo::class]
        );
    }

    public function testThrowExceptionWhenAppDataNotArrayForMultipleChoices(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Unable to transform value for property path "enum": Expected an array.');

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

    public function testSubmitSingleNull(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );

        $field->submit(null);

        self::assertTrue($field->isSynchronized());
        self::assertNull($field->getData());
        self::assertSame('', $field->getViewData());
    }

    public function testSubmitSingle(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );

        $field->submit(SimpleEnum::FIRST);

        self::assertTrue($field->isSynchronized());
        self::assertSame(SimpleEnum::get(SimpleEnum::FIRST), $field->getData());
        self::assertSame((string) SimpleEnum::FIRST, $field->getViewData());
    }

    public function testSubmitMultipleNull(): void
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

        self::assertSame([], $field->getData());
        self::assertSame([], $field->getViewData());
    }

    public function testSubmitMultipleExpanded(): void
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

        self::assertTrue($field->isSynchronized());
        self::assertSame([SimpleEnum::get(SimpleEnum::FIRST)], $field->getData());
        self::assertSame([SimpleEnum::get(SimpleEnum::FIRST)], $field->getNormData());
        self::assertTrue($field['1']->getData());
        self::assertFalse($field['2']->getData());
        self::assertSame((string) SimpleEnum::FIRST, $field['1']->getViewData());
        self::assertNull($field['2']->getViewData());
    }

    public function testSetDataSingleNull(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );
        $field->setData(null);
        self::assertNull($field->getData());
        self::assertSame('', $field->getViewData());
    }

    public function testSetDataMultipleExpandedNull(): void
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
        self::assertNull($field->getData());
        self::assertSame([], $field->getViewData());
        foreach ($field->all() as $child) {
            $this->assertSubForm($child, false, null);
        }
    }

    public function testSetDataMultipleNonExpandedNull(): void
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
        self::assertNull($field->getData());
        self::assertSame([], $field->getViewData());
    }

    public function testSetDataSingle(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => SimpleEnum::class]
        );

        $data = SimpleEnum::get(SimpleEnum::FIRST);
        $field->setData($data);

        self::assertSame($data, $field->getData());
        self::assertSame((string) SimpleEnum::FIRST, $field->getViewData());
    }

    public function testSetDataMultipleExpanded(): void
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

        self::assertSame($data, $field->getData());
        self::assertEquals([SimpleEnum::FIRST, SimpleEnum::ZERO], $field->getViewData());
        $this->assertSubForm($field->get('0'), true, (string) SimpleEnum::ZERO);
        $this->assertSubForm($field->get('1'), true, (string) SimpleEnum::FIRST);
        $this->assertSubForm($field->get('2'), false, null);
    }

    public function testSetDataExpanded(): void
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

        self::assertSame($data, $field->getData());
        self::assertSame(SimpleEnum::get(SimpleEnum::FIRST), $field->getNormData());
        self::assertSame((string) SimpleEnum::FIRST, $field->getViewData());
        $this->assertSubForm($field->get('0'), false, null);
        $this->assertSubForm($field->get('1'), true, (string) SimpleEnum::FIRST);
        $this->assertSubForm($field->get('2'), false, null);
    }

    public function testSubmitSingleAsValue(): void
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
        self::assertTrue($field->isSynchronized());
        self::assertSame(SimpleEnum::FIRST, $field->getData());
        self::assertSame((string) SimpleEnum::FIRST, $field->getViewData());
    }

    public function testSubmitMultipleAsValue(): void
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

        self::assertTrue($field->isSynchronized());

        self::assertSame([SimpleEnum::FIRST, SimpleEnum::SECOND], $field->getData());
        self::assertSame([SimpleEnum::FIRST, SimpleEnum::SECOND], $field->getNormData());

        self::assertFalse($field['0']->getData());
        self::assertTrue($field['2']->getData());
        self::assertTrue($field['2']->getData());

        self::assertNull($field['0']->getViewData());
        self::assertSame((string) SimpleEnum::FIRST, $field['1']->getViewData());
        self::assertSame((string) SimpleEnum::SECOND, $field['2']->getViewData());
    }

    public function testSubmitReadable(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => Gender::class]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        self::assertSame(Gender::UNKNOW, $choice->value);
        self::assertSame(Gender::get(Gender::UNKNOW), $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::get(Gender::MALE), $choice->data);

        $choice = $choices[2];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::get(Gender::FEMALE), $choice->data);

        $field->submit(Gender::MALE);

        self::assertTrue($field->isSynchronized());
        self::assertSame(Gender::get(Gender::MALE), $field->getData());
        self::assertSame(Gender::MALE, $field->getViewData());
    }

    public function testSubmitReadableNull(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            ['enum_class' => Gender::class]
        );
        $field->submit(null);

        self::assertTrue($field->isSynchronized());
        self::assertNull($field->getData());
        self::assertSame('', $field->getViewData());
    }

    public function testSubmitReadableAsValue(): void
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

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        self::assertSame(Gender::UNKNOW, $choice->value);
        self::assertSame(Gender::UNKNOW, $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::MALE, $choice->data);

        $choice = $choices[2];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::FEMALE, $choice->data);

        $field->submit(Gender::MALE);
        self::assertTrue($field->isSynchronized());
        self::assertSame(Gender::MALE, $field->getData());
        self::assertSame(Gender::MALE, $field->getViewData());
    }

    public function testChoicesCanBeLimitedUsingChoicesOption(): void
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

        self::assertCount(2, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::get(Gender::MALE), $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::get(Gender::FEMALE), $choice->data);

        $field->submit(Gender::UNKNOW);

        self::assertFalse($field->isSynchronized());
        self::assertNull($field->getData());
        self::assertSame(Gender::UNKNOW, $field->getViewData());
    }

    public function testChoicesAsValueCanBeLimitedUsingChoicesOption(): void
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

        self::assertCount(2, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::MALE, $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::FEMALE, $choice->data);

        $field->submit(Gender::UNKNOW);

        self::assertFalse($field->isSynchronized());
        self::assertNull($field->getData());
        self::assertSame(Gender::UNKNOW, $field->getViewData());
    }

    public function provideFormWithChoicesAsEnumValues(): iterable
    {
        yield 'EnumType with choices_as_enum_values' => [function (FormFactoryInterface $factory): FormInterface {
            return $factory->createBuilder(
                EnumType::class,
                Gender::FEMALE(),
                [
                    'choices' => ['maleLabel' => Gender::MALE, 'femaleLabel' => Gender::FEMALE],
                    'enum_class' => Gender::class,
                    'choices_as_enum_values' => true,
                ]
            )->getForm();
        }];

        yield 'ChoiceType with value to enum transformer' => [function (FormFactoryInterface $factory): FormInterface {
            return $factory->createBuilder(
                ChoiceType::class,
                Gender::FEMALE(),
                [
                    'choices' => ['maleLabel' => Gender::MALE, 'femaleLabel' => Gender::FEMALE],
                ]
            )
                ->addModelTransformer(new ValueToEnumTransformer(Gender::class))
                ->getForm()
            ;
        }];
    }

    /**
     * @dataProvider provideFormWithChoicesAsEnumValues
     */
    public function testWithChoicesAsEnumValues(callable $createForm): void
    {
        /** @var FormInterface $field */
        $field = $createForm($this->factory);
        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(2, $choices);

        $choice = $choices[0];
        self::assertSame('maleLabel', $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::MALE, $choice->data);
        self::assertSame(Gender::FEMALE(), $field->getData());

        $field->submit(Gender::MALE);

        self::assertTrue($field->isSynchronized());
        self::assertSame(Gender::MALE(), $field->getData());
        self::assertSame(Gender::MALE, $field->getViewData());
    }

    public function testWithChoicesAsEnumValuesWithoutChoicesOptions(): void
    {
        $field = $this->factory->createBuilder(
            EnumType::class,
            Gender::FEMALE(),
            [
                'enum_class' => Gender::class,
                'choices_as_enum_values' => true,
            ]
        )->getForm();

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        self::assertSame(Gender::UNKNOW, $choice->value);
        self::assertSame(Gender::UNKNOW, $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::MALE, $choice->data);

        $choice = $choices[2];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::FEMALE, $choice->data);

        self::assertSame(Gender::FEMALE(), $field->getData());

        $field->submit(Gender::MALE);

        self::assertTrue($field->isSynchronized());
        self::assertSame(Gender::MALE(), $field->getData());
        self::assertSame(Gender::MALE, $field->getViewData());
    }

    public function testAsValueAndNotChoicesAsEnumValues(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => Gender::class,
                'as_value' => true,
                'choices_as_enum_values' => false,
            ]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        self::assertSame(Gender::UNKNOW, $choice->value);
        self::assertSame(Gender::get(Gender::UNKNOW), $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::get(Gender::MALE), $choice->data);

        $choice = $choices[2];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::get(Gender::FEMALE), $choice->data);

        $field->submit(Gender::MALE);
        self::assertTrue($field->isSynchronized());
        self::assertSame(Gender::MALE, $field->getData());
        self::assertSame(Gender::MALE, $field->getViewData());
    }

    public function testAsInstancesAndChoicesAsEnumValues(): void
    {
        $field = $this->factory->create(
            EnumType::class,
            null,
            [
                'enum_class' => Gender::class,
                'as_value' => false,
                'choices_as_enum_values' => true,
            ]
        );

        $view = $field->createView();
        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(Gender::readableFor(Gender::UNKNOW), $choice->label);
        self::assertSame(Gender::UNKNOW, $choice->value);
        self::assertSame(Gender::UNKNOW, $choice->data);

        $choice = $choices[1];
        self::assertSame(Gender::readableFor(Gender::MALE), $choice->label);
        self::assertSame(Gender::MALE, $choice->value);
        self::assertSame(Gender::MALE, $choice->data);

        $choice = $choices[2];
        self::assertSame(Gender::readableFor(Gender::FEMALE), $choice->label);
        self::assertSame(Gender::FEMALE, $choice->value);
        self::assertSame(Gender::FEMALE, $choice->data);

        $field->submit(Gender::MALE);
        self::assertTrue($field->isSynchronized());
        self::assertSame(Gender::get(Gender::MALE), $field->getData());
        self::assertSame(Gender::MALE, $field->getViewData());
    }

    private function assertSubForm(FormInterface $form, $data, $viewData): void
    {
        self::assertSame($data, $form->getData(), '->getData() of sub form #' . $form->getName());
        self::assertSame($viewData, $form->getViewData(), '->getViewData() of sub form #' . $form->getName());
    }
}
