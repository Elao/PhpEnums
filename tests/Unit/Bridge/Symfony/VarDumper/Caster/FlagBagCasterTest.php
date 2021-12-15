<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\VarDumper\Caster;

use Elao\Enum\FlagBag;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

class FlagBagCasterTest extends TestCase
{
    use VarDumperTestTrait;

    public static function setUpBeforeClass(): void
    {
        putenv('DUMP_LIGHT_ARRAY=1');
    }

    public static function tearDownAfterClass(): void
    {
        putenv('DUMP_LIGHT_ARRAY');
    }

    public function testCast()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\FlagBag {
  ⚑ : Execute | Write
  type: "Elao\Enum\Tests\Fixtures\Enum\Permissions"
  value: 3
  bits: [
    1
    2
  ]
  flags: [
    Elao\Enum\Tests\Fixtures\Enum\Permissions {
      +name: "Execute"
      +value: 1
    }
    Elao\Enum\Tests\Fixtures\Enum\Permissions {
      +name: "Write"
      +value: 2
    }
  ]
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, FlagBag::from(Permissions::Execute, Permissions::Write));

        $expectedDump = <<<'EODUMP'
Elao\Enum\FlagBag {
  ⚑ : NONE
  type: "Elao\Enum\Tests\Fixtures\Enum\Permissions"
  value: 0
  bits: []
  flags: []
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, FlagBag::from(Permissions::class));
    }
}
