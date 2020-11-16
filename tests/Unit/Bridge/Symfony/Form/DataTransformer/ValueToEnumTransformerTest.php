<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\ValueToEnumTransformer;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ValueToEnumTransformerTest extends TestCase
{
    public function testThrowsExceptionIfNotEnumInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not an instance of "Elao\Enum\EnumInterface"');

        new ValueToEnumTransformer(\stdClass::class);
    }

    public function testTransform()
    {
        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $this->assertSame(SimpleEnum::FIRST, $transformer->transform(SimpleEnum::FIRST()));
    }

    public function testTransformThrowsExceptionOnNonEnum()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected instance of "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum". Got "string".');

        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $transformer->transform('foo');
    }

    public function testReverseTransform()
    {
        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $this->assertSame(SimpleEnum::FIRST(), $transformer->reverseTransform(SimpleEnum::FIRST));
    }

    public function testReverseTransformThrowsExceptionOnValueNotInEnum()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('"1" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum');

        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $transformer->reverseTransform('1');
    }
}
