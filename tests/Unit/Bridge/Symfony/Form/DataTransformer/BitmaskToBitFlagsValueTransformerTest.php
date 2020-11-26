<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\BitmaskToBitFlagsValueTransformer;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BitmaskToBitFlagsValueTransformerTest extends TestCase
{
    public function testThrowsExceptionIfNotFLaggedEnumInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"Elao\Enum\Tests\Fixtures\Enum\Gender" is not an instance of "Elao\Enum\FlaggedEnum"');

        new BitmaskToBitFlagsValueTransformer(Gender::class);
    }

    public function provideBitmaskToFlags(): iterable
    {
        yield [null, null];
        yield [Permissions::NONE, []];
        yield [Permissions::EXECUTE, [Permissions::EXECUTE]];
        yield [Permissions::EXECUTE | Permissions::READ, [Permissions::EXECUTE, Permissions::READ]];
        yield [Permissions::ALL, [Permissions::EXECUTE, Permissions::WRITE, Permissions::READ]];
    }

    /**
     * @dataProvider provideBitmaskToFlags
     */
    public function testTransform(?int $bitmask, ?array $expectedFlags): void
    {
        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        self::assertSame($expectedFlags, $transformer->transform($bitmask));
    }

    public function testTransformThrowsExceptionOnNonInteger(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected integer. Got "string"');

        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $transformer->transform('foo');
    }

    public function testTransformThrowsExceptionOnInvalifBitmask(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Invalid bitmask 999 for "Elao\Enum\Tests\Fixtures\Enum\Permissions" flagged enum.');

        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $transformer->transform(999);
    }

    public function provideFlagsToBitmask(): iterable
    {
        yield from array_map('array_reverse', iterator_to_array($this->provideBitmaskToFlags()));
    }

    /**
     * @dataProvider provideFlagsToBitmask
     */
    public function testReverseTransform(?array $flags, ?int $expectedBitmask): void
    {
        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        self::assertSame($expectedBitmask, $transformer->reverseTransform($flags));
    }

    public function testReverseTransformThrowsExceptionOnNonArray(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array. Got "string"');

        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $transformer->reverseTransform('foo');
    }

    public function testReverseTransformThrowsExceptionOnNonIntegers(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected array of integers. Got a "string" inside.');

        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $transformer->reverseTransform(['foo']);
    }

    public function testReverseTransformThrowsExceptionOnInvalifBitmask(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Invalid bitmask 56 for "Elao\Enum\Tests\Fixtures\Enum\Permissions" flagged enum.');

        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $transformer->reverseTransform([16, 32, 24]);
    }
}
