<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\SingleToCollectionFlagEnumTransformer;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SingleToCollectionFlagEnumTransformerTest extends TestCase
{
    public function testThrowsExceptionIfNotFLaggedEnumInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"Elao\Enum\Tests\Fixtures\Enum\Gender" is not an instance of "Elao\Enum\FlaggedEnum"');

        new SingleToCollectionFlagEnumTransformer(Gender::class);
    }

    public function provideSingleToCollection()
    {
        return [
            [null, null],
            [Permissions::get(Permissions::NONE), []],
            [Permissions::get(Permissions::EXECUTE), [Permissions::get(Permissions::EXECUTE)]],
            [
                Permissions::get(Permissions::EXECUTE | Permissions::READ),
                [Permissions::get(Permissions::EXECUTE), Permissions::get(Permissions::READ)],
            ],
            [
                Permissions::get(Permissions::ALL),
                [
                    Permissions::get(Permissions::EXECUTE),
                    Permissions::get(Permissions::WRITE),
                    Permissions::get(Permissions::READ),
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideSingleToCollection
     */
    public function testTransform($singleEnum, $expectedEnums)
    {
        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $this->assertSame($expectedEnums, $transformer->transform($singleEnum));
    }

    public function testTransformThrowsExceptionOnInvalidInstance()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected instance of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got "Elao\Enum\Tests\Fixtures\Enum\Gender"');

        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->transform(Gender::get(Gender::MALE));
    }

    public function provideCollectionToSingle()
    {
        return array_map('array_reverse', $this->provideSingleToCollection());
    }

    /**
     * @dataProvider provideCollectionToSingle
     */
    public function testReverseTransform($enums, $expectedEnum)
    {
        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $this->assertSame($expectedEnum, $transformer->reverseTransform($enums));
    }

    public function testReverseTransformThrowsExceptionOnInvalidInstance()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got a "Elao\Enum\Tests\Fixtures\Enum\Gender" inside.');

        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->reverseTransform([Permissions::get(Permissions::EXECUTE), Gender::get(Gender::MALE)]);
    }

    public function testReverseTransformThrowsExceptionOnNonArray()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array. Got "Elao\Enum\Tests\Fixtures\Enum\Permissions"');

        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->reverseTransform(Permissions::get(Permissions::EXECUTE));
    }
}
