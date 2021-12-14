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

use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use Symfony\Component\Form\Test\TypeTestCase;

class EnumTypeTest extends TypeTestCase
{
    public function testChoiceLabel(): void
    {
        $form = $this->factory->create($this->getTestedType(), null, [
            'multiple' => false,
            'expanded' => true,
            'class' => Suit::class,
        ]);

        $view = $form->createView();

        $this->assertSame('suit.hearts', $view->children[0]->vars['label']);
    }

    protected function getTestedType()
    {
        return EnumType::class;
    }

    protected function getTestOptions(): array
    {
        return ['class' => Suit::class];
    }
}
