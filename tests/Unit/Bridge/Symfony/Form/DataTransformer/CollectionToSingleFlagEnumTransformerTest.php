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

class CollectionToSingleFlagEnumTransformerTest extends \PHPUnit_Framework_TestCase
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
            [Permissions::create(Permissions::NONE), []],
            [Permissions::create(Permissions::EXECUTE), [Permissions::create(Permissions::EXECUTE)]],
            [
                Permissions::create(Permissions::EXECUTE | Permissions::READ),
                [Permissions::create(Permissions::EXECUTE), Permissions::create(Permissions::READ)],
            ],
            [
                Permissions::create(Permissions::ALL),
                [
                    Permissions::create(Permissions::EXECUTE),
                    Permissions::create(Permissions::WRITE),
                    Permissions::create(Permissions::READ),
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
}
