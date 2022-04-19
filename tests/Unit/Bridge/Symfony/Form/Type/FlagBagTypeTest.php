<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\Type;

use Elao\Enum\Bridge\Symfony\Form\Type\FlagBagType;
use Elao\Enum\FlagBag;
use Elao\Enum\Tests\Fixtures\Enum\PermissionsReadable;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class FlagBagTypeTest extends FormIntegrationTestCase
{
    public function testSetMutipleToFalseThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The "multiple" option of the "Elao\Enum\Bridge\Symfony\Form\Type\FlagBagType" form type cannot be set to false.');

        $this->factory->create(
            FlagBagType::class,
            null,
            [
                'class' => PermissionsReadable::class,
                'multiple' => false,
            ]
        );
    }

    public function testSubmit(): void
    {
        $field = $this->factory->create(
            FlagBagType::class,
            FlagBag::from(PermissionsReadable::Execute, PermissionsReadable::Write),
            [
                'class' => PermissionsReadable::class,
            ]
        );

        $view = $field->createView();

        self::assertEquals([PermissionsReadable::Execute->value, PermissionsReadable::Write->value], $view->vars['value']);
        self::assertSame([
            PermissionsReadable::Execute,
            PermissionsReadable::Write,
        ], $view->vars['data']);

        /** @var ChoiceView[] $choices */
        $choices = $view->vars['choices'];

        self::assertCount(3, $choices);

        $choice = $choices[0];
        self::assertSame(PermissionsReadable::Execute->getReadable(), $choice->label);
        self::assertSame((string) PermissionsReadable::Execute->value, $choice->value);
        self::assertSame(PermissionsReadable::Execute, $choice->data);

        $choice = $choices[1];
        self::assertSame(PermissionsReadable::Write->getReadable(), $choice->label);
        self::assertSame((string) PermissionsReadable::Write->value, $choice->value);
        self::assertSame(PermissionsReadable::Write, $choice->data);

        $choice = $choices[2];
        self::assertSame(PermissionsReadable::Read->getReadable(), $choice->label);
        self::assertSame((string) PermissionsReadable::Read->value, $choice->value);
        self::assertSame(PermissionsReadable::Read, $choice->data);

        $field->submit([PermissionsReadable::Write->value, PermissionsReadable::Read->value]);

        self::assertTrue($field->isSynchronized());
        self::assertEquals(FlagBag::from(PermissionsReadable::Write, PermissionsReadable::Read), $field->getData());
        self::assertEquals([PermissionsReadable::Write->value, PermissionsReadable::Read->value], array_values($field->getViewData()));
    }
}
