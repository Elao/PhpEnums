<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\ODM\Types;

use Doctrine\ODM\MongoDB\Types\Type;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types\SimpleCollectionEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;

class CollectionEnumTypeTest extends TestCase
{
    /** @var SimpleCollectionEnumType */
    protected $type;
    protected const NAME = 'simple_collection';

    public static function setUpBeforeClass(): void
    {
        Type::addType(self::NAME, SimpleCollectionEnumType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->type = Type::getType(self::NAME);
    }

    public function testConvertToDatabaseValue(): void
    {
        self::assertSame([0, 2], $this->type->convertToDatabaseValue([
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
        ]));
    }

    public function testConvertToDatabaseValueReturnsUniqueValues(): void
    {
        self::assertSame([0, 2], $this->type->convertToDatabaseValue([
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
        ]));
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null));
    }

    public function testConvertToPHPValue(): void
    {
        self::assertSame(
            [SimpleEnum::ZERO(), SimpleEnum::SECOND()],
            $this->type->convertToPHPValue([0, 2])
        );
    }

    public function testConvertToPHPValueReturnsUniqueValue(): void
    {
        self::assertSame(
            [SimpleEnum::ZERO(), SimpleEnum::SECOND()],
            $this->type->convertToPHPValue([0, 2, 0, 2])
        );
    }

    public function testConvertToPHPValueOnNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null));
    }

    public function testConvertToPHPValueOnEmptyArray(): void
    {
        self::assertSame([], $this->type->convertToPHPValue([]));
    }
}
