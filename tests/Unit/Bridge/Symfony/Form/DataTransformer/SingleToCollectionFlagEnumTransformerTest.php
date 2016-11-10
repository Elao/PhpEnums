<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\SingleToCollectionFlagEnumTransformer;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;

class SingleToCollectionFlagEnumTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidArgumentException
     * @expectedExceptionMessage "Elao\Enum\Tests\Fixtures\Enum\Gender" is not an instance of "Elao\Enum\FlaggedEnum"
     */
    public function testThrowsExceptionIfNotFLaggedEnumInstance()
    {
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

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Expected instance of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got "Elao\Enum\Tests\Fixtures\Enum\Gender"
     */
    public function testTransformThrowsExceptionOnInvalidInstance()
    {
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

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Expected array of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got a "Elao\Enum\Tests\Fixtures\Enum\Gender" inside.
     */
    public function testReverseTransformThrowsExceptionOnInvalidInstance()
    {
        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->reverseTransform([Permissions::get(Permissions::EXECUTE), Gender::get(Gender::MALE)]);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Expected array. Got "Elao\Enum\Tests\Fixtures\Enum\Permissions"
     */
    public function testReverseTransformThrowsExceptionOnNonArray()
    {
        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->reverseTransform(Permissions::get(Permissions::EXECUTE));
    }
}
