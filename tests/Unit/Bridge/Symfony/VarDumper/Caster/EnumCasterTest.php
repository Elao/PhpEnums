<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\VarDumper\Caster;

use Elao\Enum\EnumInterface;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

class EnumCasterTest extends TestCase
{
    use VarDumperTestTrait;

    public static function setUpBeforeClass()
    {
        putenv('DUMP_LIGHT_ARRAY=1');
    }

    public static function tearDownAfterClass()
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
  #flags:%S [
   %S 1
   %S 2
  ]
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, Permissions::get(Permissions::EXECUTE | Permissions::WRITE));

        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Permissions {
  ⚑ : NONE
  readable: "None"
  #value: 0
  #flags:%S []
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, Permissions::get(Permissions::NONE));

        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Permissions {
  ⚑ : EXECUTE | WRITE | READ
  readable: "All permissions"
  #value: 7
  #flags:%A [
   %S 1
   %S 2
   %S 4
  ]
}
EODUMP;

        $this->assertDumpMatchesFormat($expectedDump, Permissions::get(Permissions::ALL));
    }

    public function testCastAsHtml()
    {
        $enum = Permissions::get(Permissions::ALL);

        $dump = $this->dumpAsHtml($enum);

        $expectedDump = <<<'EODUMP'
<header></header><boundary><abbr title="Elao\Enum\Tests\Fixtures\Enum\Permissions" class=sf-dump-note>Permissions</abbr> {<samp>
  <span class=sf-dump-meta>&#9873; </span>: <span class=sf-dump-const title="7">EXECUTE | WRITE | READ</span>
  <span class=sf-dump-meta>readable</span>: "<span class=sf-dump-str title="15 characters">All permissions</span>"
  #<span class=sf-dump-protected title="Protected property">value</span>: <span class=sf-dump-num>7</span>
  #<span class=sf-dump-protected title="Protected property">flags</span>:%S [<samp>
    %S<span class=sf-dump-const title="%SEXECUTE%S">1</span>
    %S<span class=sf-dump-const title="%SWRITE%S">2</span>
    %S<span class=sf-dump-const title="%SREAD%S">4</span>
  </samp>]
</samp>}
</boundary>

EODUMP;

        $this->assertStringMatchesFormat($expectedDump, $dump);
    }

    private function dumpAsHtml($value): string
    {
        $cloner = new VarCloner();
        $cloner->setMaxItems(-1);

        $configure = function (HtmlDumper $dumper) {
            $dumper->setDumpHeader('<header></header>');
            $dumper->setDumpBoundaries('<boundary>', '</boundary>');
        };

        // To remove once symfony/var-dumper < 3.2 support is dropped to simplify things
        if (!method_exists(HtmlDumper::class, 'setDisplayOptions')) {
            $h = fopen('php://memory', 'r+b');
            $dumper = new HtmlDumper($h);
            $configure($dumper);
            $dumper->dump($cloner->cloneVar($value)->withRefHandles(false), $h);
            $dump = stream_get_contents($h, -1, 0);
            fclose($h);

            return $dump;
        }

        // Once symfony/var-dumper < 3.1 support is dropped, the light array flag can be used
        // and StringMatches assertions removed in favor of Identical/Equals assertions
        $flags = defined(AbstractDumper::class . '::DUMP_LIGHT_ARRAY') ? AbstractDumper::DUMP_LIGHT_ARRAY : null;
        $dumper = new HtmlDumper(null, null, $flags);
        $configure($dumper);

        return $dumper->dump($cloner->cloneVar($value)->withRefHandles(false), true);
    }
}
