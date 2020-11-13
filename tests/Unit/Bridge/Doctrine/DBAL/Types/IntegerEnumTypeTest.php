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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\SimpleEnumType;
use Elao\Enum\Tests\Fixtures\Enum\SimpleEnum;
use Elao\Enum\Tests\TestCase;

class IntegerEnumTypeTest extends TestCase
{
    /** @var AbstractPlatform */
    protected $platform;

    /** @var SimpleEnumType */
    protected $type;

    public static function setUpBeforeClass(): void
    {
        Type::addType(SimpleEnumType::NAME, SimpleEnumType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->platform = $this->prophesize(AbstractPlatform::class)->reveal();
        $this->type = Type::getType(SimpleEnumType::NAME);
    }

    public function testConvertToDatabaseValue()
    {
        $databaseValue = $this->type->convertToDatabaseValue(SimpleEnum::get(SimpleEnum::FIRST), $this->platform);
        $this->assertSame(1, $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull()
    {
        $databaseValue = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertSame(0, $databaseValue);
    }

    public function testConvertToPHPValue()
    {
        $PHPValue = $this->type->convertToPHPValue(1, $this->platform);
        $this->assertSame(SimpleEnum::get(SimpleEnum::FIRST), $PHPValue);
    }

    public function testConvertToPHPValueOnNull()
    {
        $PHPValue = $this->type->convertToPHPValue(null, $this->platform);
        $this->assertSame(SimpleEnum::get(SimpleEnum::ZERO), $PHPValue);
    }

    public function testRequiresSQLCommentHintIsTrue()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testIntegerBindingType()
    {
        $this->assertSame(\PDO::PARAM_INT, $this->type->getBindingType());
    }
}
