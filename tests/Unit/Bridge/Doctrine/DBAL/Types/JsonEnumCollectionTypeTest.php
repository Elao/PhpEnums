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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\SimpleJsonCollectionEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;

class JsonEnumCollectionTypeTest extends TestCase
{
    /** @var AbstractPlatform */
    protected $platform;

    /** @var SimpleJsonCollectionEnumType */
    protected $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType(SimpleJsonCollectionEnumType::NAME, SimpleJsonCollectionEnumType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->platform = $this->prophesize(AbstractPlatform::class)->reveal();
        $this->type = Type::getType(SimpleJsonCollectionEnumType::NAME);
    }

    public function testConvertToDatabaseValue(): void
    {
        self::assertSame('[0,2]', $this->type->convertToDatabaseValue([
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
        ], $this->platform));
    }

    public function testConvertToDatabaseValueReturnsUniqueValues(): void
    {
        self::assertSame('[0,2]', $this->type->convertToDatabaseValue([
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
            SimpleEnum::ZERO(),
            SimpleEnum::SECOND(),
        ], $this->platform));
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testConvertToPHPValue(): void
    {
        self::assertSame(
            [SimpleEnum::ZERO(), SimpleEnum::SECOND()],
            $this->type->convertToPHPValue('[0,2]', $this->platform)
        );
    }

    public function testConvertToPHPValueReturnsUniqueValue(): void
    {
        self::assertSame(
            [SimpleEnum::ZERO(), SimpleEnum::SECOND()],
            $this->type->convertToPHPValue('[0,2,0,2]', $this->platform)
        );
    }

    public function testConvertToPHPValueOnNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueOnEmptyArray(): void
    {
        self::assertSame([], $this->type->convertToPHPValue('[]', $this->platform));
    }
}
