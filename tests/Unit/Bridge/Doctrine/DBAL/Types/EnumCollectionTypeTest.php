<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\SimpleEnumCollectionType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use PHPUnit\Framework\TestCase;

class EnumCollectionTypeTest extends TestCase
{
    /** @var AbstractPlatform */
    protected $platform;

    /** @var SimpleEnumCollectionType */
    protected $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType(SimpleEnumCollectionType::NAME, SimpleEnumCollectionType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->platform = $this->prophesize(AbstractPlatform::class)->reveal();
        $this->type = Type::getType(SimpleEnumCollectionType::NAME);
    }

    public function testConvertToDatabaseValue()
    {
        self::assertSame('[0,2]', $this->type->convertToDatabaseValue([
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
        ], $this->platform));
    }

    public function testConvertToDatabaseValueOnNull()
    {
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testConvertToPHPValue()
    {
        self::assertSame(
            [SimpleEnum::ZERO(), SimpleEnum::SECOND()],
            $this->type->convertToPHPValue('[0,2]', $this->platform)
        );
    }

    public function testConvertToPHPValueOnNull()
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueOnEmptyArray()
    {
        self::assertSame([], $this->type->convertToPHPValue('[]', $this->platform));
    }
}
