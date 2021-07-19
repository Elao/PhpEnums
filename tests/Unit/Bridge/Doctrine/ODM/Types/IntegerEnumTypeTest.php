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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types\SimpleEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;

class IntegerEnumTypeTest extends TestCase
{
    /** @var SimpleEnumType */
    protected $type;
    protected const NAME = 'simple_enum_collection';

    public static function setUpBeforeClass(): void
    {
        Type::addType(self::NAME, SimpleEnumType::class);
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
        $databaseValue = $this->type->convertToDatabaseValue(SimpleEnum::get(SimpleEnum::FIRST));
        self::assertSame(1, $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null));
    }

    public function testConvertToPHPValue(): void
    {
        $PHPValue = $this->type->convertToPHPValue(1);
        self::assertSame(SimpleEnum::get(SimpleEnum::FIRST), $PHPValue);
    }

    public function testConvertToPHPValueOnNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null));
    }
}
