<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\PhpTranslation\Extractor;

use Elao\Enum\Bridge\PhpTranslation\Extractor\ReflectionEnumExtractor;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;
use PHPUnit\Framework\TestCase;
use Translation\Extractor\Model\SourceCollection;
use Translation\Extractor\Model\SourceLocation;

class ReflectionEnumExtractorTest extends TestCase
{
    public function testGetSourceLocations()
    {
        $classLoader = function () {
            return [Permissions::class, Gender::class];
        };
        $enumExtractor = new ReflectionEnumExtractor($classLoader);
        $sourceCollection = new SourceCollection();
        $enumExtractor->getSourceLocations($sourceCollection);

        $this->assertSourceCollectionEquals(
            [
                'Execute',
                'Write',
                'Read',
                'Read & write',
                'Read & execute',
                'All permissions',
                'Unknown',
                'Male',
                'Female',
            ],
            $sourceCollection
        );
    }

    private function assertSourceCollectionEquals(array $expected, SourceCollection $sourceCollection)
    {
        $this->assertEquals(
            $expected,
            array_map(
                function (SourceLocation $sourceLocation) {
                    return $sourceLocation->getMessage();
                },
                iterator_to_array($sourceCollection)
            )
        );
    }
}
