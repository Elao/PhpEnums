<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\FlagBagToCollectionTransformer;
use Elao\Enum\FlagBag;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\PermissionsMissingBit;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FlagBagToCollectionTransformerTest extends TestCase
{
    public function testThrowsExceptionIfNotFLaggedEnumInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FlagBagToCollectionTransformer(Suit::class);
    }

    public function provideFlagBagToCollection(): \Traversable
    {
        yield [null, null];
        yield [new FlagBag(Permissions::class, FlagBag::NONE), []];
        yield [FlagBag::from(Permissions::Execute), [Permissions::Execute]];
        yield [FlagBag::from(Permissions::Execute, Permissions::Read), [Permissions::Execute, Permissions::Read]];
        yield [FlagBag::fromAll(Permissions::class), [Permissions::Execute, Permissions::Write, Permissions::Read]];
    }

    /**
     * @dataProvider provideFlagBagToCollection
     */
    public function testTransform(?FlagBag $flagBag, ?array $expectedEnums): void
    {
        $transformer = new FlagBagToCollectionTransformer(Permissions::class);

        self::assertSame($expectedEnums, $transformer->transform($flagBag));
    }

    public function testTransformThrowsExceptionOnInvalidType(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected instance of "Elao\Enum\FlagBag". Got "string".');

        $transformer = new FlagBagToCollectionTransformer(Permissions::class);

        $transformer->transform('foo');
    }

    public function testTransformThrowsExceptionOnInvalidInstance(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected FlagBag instance of "Elao\Enum\Tests\Fixtures\Enum\Permissions" values. Got FlagBag instance of "Elao\Enum\Tests\Fixtures\Enum\PermissionsMissingBit" values.');

        $transformer = new FlagBagToCollectionTransformer(Permissions::class);

        $transformer->transform(new FlagBag(PermissionsMissingBit::class, FlagBag::NONE));
    }

    public function provideCollectionToFlagBag(): \Traversable
    {
        yield from array_map(array_reverse(...), iterator_to_array($this->provideFlagBagToCollection()));
    }

    /**
     * @dataProvider provideCollectionToFlagBag
     */
    public function testReverseTransform(?array $enums, ?FlagBag $flagBag): void
    {
        $transformer = new FlagBagToCollectionTransformer(Permissions::class);

        self::assertEquals($flagBag, $transformer->reverseTransform($enums));
    }

    public function testReverseTransformThrowsExceptionOnInvalidInstance(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array of "Elao\Enum\Tests\Fixtures\Enum\Permissions". Got a "Elao\Enum\Tests\Fixtures\Enum\Suit" inside.');

        $transformer = new FlagBagToCollectionTransformer(Permissions::class);

        $transformer->reverseTransform([Permissions::Execute, Suit::Clubs]);
    }

    public function testReverseTransformThrowsExceptionOnNonArray(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array. Got "Elao\Enum\Tests\Fixtures\Enum\Permissions"');

        $transformer = new FlagBagToCollectionTransformer(Permissions::class);

        $transformer->reverseTransform(Permissions::Execute);
    }
}
