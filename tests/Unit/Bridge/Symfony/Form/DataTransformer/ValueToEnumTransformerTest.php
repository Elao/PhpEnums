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
use PHPUnit\Framework\TestCase;

class ValueToEnumTransformerTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidArgumentException
     * @expectedExceptionMessage "stdClass" is not an instance of "Elao\Enum\EnumInterface"
     */
    public function testThrowsExceptionIfNotEnumInstance()
    {
        new ValueToEnumTransformer(\stdClass::class);
    }

    public function testTransform()
    {
        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $this->assertSame(SimpleEnum::FIRST, $transformer->transform(SimpleEnum::FIRST()));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Expected instance of "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum". Got "string".
     */
    public function testTransformThrowsExceptionOnNonEnum()
    {
        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $transformer->transform('foo');
    }

    public function testReverseTransform()
    {
        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $this->assertSame(SimpleEnum::FIRST(), $transformer->reverseTransform(SimpleEnum::FIRST));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage "1" is not an acceptable value for "Elao\Enum\Tests\Fixtures\Enum\SimpleEnum
     */
    public function testReverseTransformThrowsExceptionOnValueNotInEnum()
    {
        $transformer = new ValueToEnumTransformer(SimpleEnum::class);

        $transformer->reverseTransform('1');
    }
}
