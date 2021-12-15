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

use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

class ReadableEnumCasterTest extends TestCase
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
        self::assertArrayHasKey(
            ReadableEnumInterface::class,
            VarCloner::$defaultCasters,
            'the caster is registered through composer autoload files'
        );
    }

    public function testCastReadable()
    {
        $expectedDump = <<<'EODUMP'
Elao\Enum\Tests\Fixtures\Enum\Suit {
  +name: "Spades"
  +value: "S"
  readable: "suit.spades"
}
EODUMP;

        $this->assertDumpEquals($expectedDump, Suit::Spades);
    }

    public function testCastAsHtml()
    {
        $dump = $this->dumpAsHtml(Suit::Spades);

        $expectedDump = <<<'EODUMP'
"""
<header></header><boundary><span class=sf-dump-note>Elao\Enum\Tests\Fixtures\Enum\Suit</span> {<samp data-depth=1 class=sf-dump-expanded>\n
  +<span class=sf-dump-public title="Public property">name</span>: "<span class=sf-dump-str title="6 characters">Spades</span>"\n
  +<span class=sf-dump-public title="Public property">value</span>: "<span class=sf-dump-str>S</span>"\n
  <span class=sf-dump-meta>readable</span>: "<span class=sf-dump-str title="11 characters">suit.spades</span>"\n
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
