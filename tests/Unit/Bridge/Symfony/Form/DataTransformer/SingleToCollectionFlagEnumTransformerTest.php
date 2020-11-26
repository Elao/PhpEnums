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
use Elao\Enum\EnumInterface;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SingleToCollectionFlagEnumTransformerTest extends TestCase
{
    public function testThrowsExceptionIfNotFLaggedEnumInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"Elao\Enum\Tests\Fixtures\Enum\Gender" is not an instance of "Elao\Enum\FlaggedEnum"');

        new SingleToCollectionFlagEnumTransformer(Gender::class);
    }

    public function provideSingleToCollection(): iterable
    {
        yield [null, null];
        yield [Permissions::get(Permissions::NONE), []];
        yield [Permissions::get(Permissions::EXECUTE), [Permissions::get(Permissions::EXECUTE)]];
        yield [Permissions::get(Permissions::EXECUTE | Permissions::READ), [
            Permissions::get(Permissions::EXECUTE),
            Permissions::get(Permissions::READ),
        ]];
        yield [Permissions::get(Permissions::ALL), [
            Permissions::get(Permissions::EXECUTE),
            Permissions::get(Permissions::WRITE),
            Permissions::get(Permissions::READ),
        ]];
    }

    /**
     * @dataProvider provideSingleToCollection
     */
    public function testTransform(?EnumInterface $singleEnum, ?array $expectedEnums): void
    {
        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        self::assertSame($expectedEnums, $transformer->transform($singleEnum));
    }

    public function testTransformThrowsExceptionOnInvalidInstance(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected instance of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got "Elao\Enum\Tests\Fixtures\Enum\Gender"');

        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->transform(Gender::get(Gender::MALE));
    }

    public function provideCollectionToSingle(): iterable
    {
        yield from array_map('array_reverse', iterator_to_array($this->provideSingleToCollection()));
    }

    /**
     * @dataProvider provideCollectionToSingle
     */
    public function testReverseTransform(?array $enums, ?EnumInterface $expectedEnum): void
    {
        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        self::assertSame($expectedEnum, $transformer->reverseTransform($enums));
    }

    public function testReverseTransformThrowsExceptionOnInvalidInstance(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got a "Elao\Enum\Tests\Fixtures\Enum\Gender" inside.');

        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->reverseTransform([Permissions::get(Permissions::EXECUTE), Gender::get(Gender::MALE)]);
    }

    public function testReverseTransformThrowsExceptionOnNonArray(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array. Got "Elao\Enum\Tests\Fixtures\Enum\Permissions"');

        $transformer = new SingleToCollectionFlagEnumTransformer(Permissions::class);

        $transformer->reverseTransform(Permissions::get(Permissions::EXECUTE));
    }
}
