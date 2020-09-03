<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\VarDumper\Caster;

use Elao\Enum\Enum;
use Elao\Enum\EnumInterface;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\Php71CastedEnumWIthPrivateConstants;
use Elao\Enum\Tests\Fixtures\Enum\SimpleChoiceEnumFromDumEnum;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

class EnumCasterTest extends TestCase
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

    public function testCasterIsRegistered()
    {
        $this->assertArrayHasKey(
            EnumInterface::class,
            VarCloner::$defaultCasters,
            'the caster is registered through composer autoload files'
        );
    }

    public function testCast()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\SimpleEnum {
  ⚑ : FIRST
  #value: 1
}
EODUMP;

        $this->assertDumpEquals($expectedDump, SimpleEnum::get(SimpleEnum::FIRST));
    }

    /**
     * @requires PHP 7.1
     */
    public function testCastWithPrivateConstants()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Php71CastedEnumWIthPrivateConstants {
  ⚑ : FOO
  #value: "foo"
}
EODUMP;

        $this->assertDumpEquals($expectedDump, Php71CastedEnumWIthPrivateConstants::get(Php71CastedEnumWIthPrivateConstants::FOO));
    }

    public function testCastWithPublicNonEnumerableConstant()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Unit\Bridge\Symfony\VarDumper\Caster\EnumWithNonEnumerablePublicConstants {
  ⚑ : BAR
  #value: "bar"
}
EODUMP;

        $this->assertDumpEquals($expectedDump, EnumWithNonEnumerablePublicConstants::get(EnumWithNonEnumerablePublicConstants::BAR));
    }

    public function testCastReadable()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Gender {
  ⚑ : MALE
  readable: "Male"
  #value: "male"
}
EODUMP;

        $this->assertDumpEquals($expectedDump, Gender::get(Gender::MALE));
    }

    public function testCastFlaggedEnum()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Permissions {
  ⚑ : EXECUTE | WRITE
  readable: "Execute; Write"
  #value: 3
  #flags: [
    1
    2
  ]
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, Permissions::get(Permissions::EXECUTE | Permissions::WRITE));

        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Permissions {
  ⚑ : NONE
  readable: "None"
  #value: 0
  #flags: []
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, Permissions::get(Permissions::NONE));

        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Permissions {
  ⚑ : EXECUTE | WRITE | READ
  readable: "All permissions"
  #value: 7
  #flags: [
    1
    2
    4
  ]
}
EODUMP;

        $this->assertDumpEquals($expectedDump, Permissions::get(Permissions::ALL));
    }

    public function testCastWithDiscoveringFromOtherClasses()
    {
        $expectedDump = <<<'EODUMP'
[
  Elao\Enum\Tests\Fixtures\Enum\SimpleChoiceEnumFromDumEnum {
    ⚑ : Foo
    readable: "Foo"
    #value: "foo"
  }
  Elao\Enum\Tests\Fixtures\Enum\SimpleChoiceEnumFromDumEnum {
    ⚑ : BAR
    readable: "Bar"
    #value: "bar"
  }
  Elao\Enum\Tests\Fixtures\Enum\SimpleChoiceEnumFromDumEnum {
    ⚑ : BAZ
    readable: "Baz"
    #value: "baz"
  }
]
EODUMP;

        $this->assertDumpEquals($expectedDump, SimpleChoiceEnumFromDumEnum::instances());
    }

    public function testCastAsHtml()
    {
        $enum = Permissions::get(Permissions::ALL);

        $dump = $this->dumpAsHtml($enum);

        $expectedDump = <<<'EODUMP'
"""
<header></header><boundary>%aElao\Enum\Tests\Fixtures\Enum\Permissions%a {<samp>\n
  <span class=sf-dump-meta>&#9873; </span>: <span class=sf-dump-const title="7">EXECUTE | WRITE | READ</span>\n
  <span class=sf-dump-meta>readable</span>: "<span class=sf-dump-str title="15 characters">All permissions</span>"\n
  #<span class=sf-dump-protected title="Protected property">value</span>: <span class=sf-dump-num>7</span>\n
  #<span class=sf-dump-protected title="Protected property">flags</span>: [<samp>\n
    <span class=sf-dump-const title="EXECUTE">1</span>\n
    <span class=sf-dump-const title="WRITE">2</span>\n
    <span class=sf-dump-const title="READ">4</span>\n
  </samp>]\n
</samp>}\n
</boundary>\n
"""
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, $dump);
    }

    private function dumpAsHtml($value): string
    {
        $cloner = new VarCloner();
        $cloner->setMaxItems(-1);

        $configure = static function (HtmlDumper $dumper) {
            $dumper->setDumpHeader('<header></header>');
            $dumper->setDumpBoundaries('<boundary>', '</boundary>');
        };

        $flags = AbstractDumper::DUMP_LIGHT_ARRAY;
        $dumper = new HtmlDumper(null, null, $flags);
        $configure($dumper);

        return $dumper->dump($cloner->cloneVar($value)->withRefHandles(false), true);
    }
}

class EnumWithNonEnumerablePublicConstants extends Enum
{
    const FOO = 'foo';
    const BAR = 'bar';

    const NOT_AN_ENUMERABLE_VALUE = [self::FOO, self::BAR];

    public static function values(): array
    {
        return [self::FOO, self::BAR];
    }
}
