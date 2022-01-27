<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests;

use PHPUnit\Framework\Assert;

trait IterableAssertionsTrait
{
    private static function iterates(iterable $iterable): void
    {
        foreach ($iterable as $_) {
        }
    }

    private static function assertIterablesMatch(iterable $expected, iterable $iterable)
    {
        $keys = [];
        $values = [];
        foreach ($iterable as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }

        $expectedKeys = [];
        $expectedValues = [];
        foreach ($expected as $key => $value) {
            $expectedKeys[] = $key;
            $expectedValues[] = $value;
        }

        Assert::assertSame($keys, $expectedKeys, 'iterator keys are identical');
        Assert::assertSame($values, $expectedValues, 'iterator values are identical');
    }
}
