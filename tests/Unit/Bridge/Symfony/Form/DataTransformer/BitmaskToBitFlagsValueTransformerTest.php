<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Form\DataTransformer;

use Elao\Enum\Bridge\Symfony\Form\DataTransformer\BitmaskToBitFlagsValueTransformer;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;

class BitmaskToBitFlagsValueTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidArgumentException
     * @expectedExceptionMessage "Elao\Enum\Tests\Fixtures\Enum\Gender" is not an instance of "Elao\Enum\FlaggedEnum"
     */
    public function testThrowsExceptionIfNotFLaggedEnumInstance()
    {
        new BitmaskToBitFlagsValueTransformer(Gender::class);
    }

    public function provideBitmaskToFlags()
    {
        return [
            [null, null],
            [Permissions::NONE, []],
            [Permissions::EXECUTE, [Permissions::EXECUTE]],
            [Permissions::EXECUTE | Permissions::READ, [Permissions::EXECUTE, Permissions::READ]],
            [Permissions::ALL, [Permissions::EXECUTE, Permissions::WRITE, Permissions::READ]],
        ];
    }

    /**
     * @dataProvider provideBitmaskToFlags
     */
    public function testTransform($bitmask, $expectedFlags)
    {
        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $this->assertSame($expectedFlags, $transformer->transform($bitmask));
    }

    public function provideFlagsToBitmask()
    {
        return array_map('array_reverse', $this->provideBitmaskToFlags());
    }

    /**
     * @dataProvider provideFlagsToBitmask
     */
    public function testReverseTransform($flags, $expectedBitmask)
    {
        $transformer = new BitmaskToBitFlagsValueTransformer(Permissions::class);

        $this->assertSame($expectedBitmask, $transformer->reverseTransform($flags));
    }
}
