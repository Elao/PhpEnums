<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Runner\Version;
use Prophecy\PhpUnit\ProphecyTrait;

if (version_compare(Version::id(), '9.0.0', '<')) {
    class TestCase extends \PHPUnit\Framework\TestCase
    {
        public static function assertFileDoesNotExist($filename, $message = '')
        {
            Assert::assertFileNotExists($filename, $message);
        }
    }
} else {
    class TestCase extends \PHPUnit\Framework\TestCase
    {
        use ProphecyTrait;
    }
}
