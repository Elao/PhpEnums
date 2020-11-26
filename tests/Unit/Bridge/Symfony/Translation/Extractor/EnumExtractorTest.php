<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Translation\Extractor;

use Elao\Enum\Bridge\Symfony\Translation\Extractor\EnumExtractor;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Translation\MessageCatalogue;

class EnumExtractorTest extends TestCase
{
    private $namespaceToDirMap = ['Elao\Enum\Tests\Unit\Bridge\Symfony\Translation\Extractor\Enum' => __DIR__ . '/Enum'];

    /** @var EnumExtractor */
    private $extractor;

    protected function setUp(): void
    {
        $this->extractor = new EnumExtractor(
            $this->namespaceToDirMap,
            'messages_test',
            '*Enum.php',
            [__DIR__ . '/Enum/Ignore/*']
        );

        $this->extractor->setPrefix('__');
    }

    public function testRunsOnlyOnce(): void
    {
        $catalog1 = new MessageCatalogue('en');
        $this->extractor->extract('foo', $catalog1);
        $catalog2 = new MessageCatalogue('en');
        $this->extractor->extract('bar', $catalog2);

        self::assertCount(1, $catalog1->all());
        self::assertEmpty($catalog2->all());
    }

    public function testInvalidDirectoryThrowsException(): void
    {
        $this->expectException(DirectoryNotFoundException::class);

        $extractor = new EnumExtractor(['bar' => 'foo'], 'domain', '*.php', []);
        $extractor->extract('baz', new MessageCatalogue('en'));
    }

    public function testGenerateTranslations(): void
    {
        $catalog = new MessageCatalogue('en');
        $this->extractor->extract('foo', $catalog);

        self::assertEquals(
            [
                'messages_test' => ['trans_readable_enum' => '__trans_readable_enum'],
            ],
            $catalog->all()
        );

        self::assertEquals(
            [
                'trans_readable_enum' => ['sources' => [str_replace(\DIRECTORY_SEPARATOR, '/', __DIR__) . '/Enum/ReadableEnum.php:readable_enum']],
            ],
            $catalog->getMetadata('', 'messages_test')
        );
    }
}
