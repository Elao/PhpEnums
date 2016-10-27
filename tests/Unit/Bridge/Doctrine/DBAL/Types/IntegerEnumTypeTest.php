<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Elao\Enum\Tests\Fixtures\Unit\Bridge\Doctrine\DBAL\Types\SimpleEnumType;
use Elao\Enum\Tests\Fixtures\Unit\EnumTest\SimpleEnum;

class IntegerEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var AbstractPlatform */
    protected $platform;

    /** @var SimpleEnumType */
    protected $type;

    public static function setUpBeforeClass()
    {
        Type::addType(SimpleEnumType::NAME, SimpleEnumType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = $this->prophesize(AbstractPlatform::class)->reveal();
        $this->type = Type::getType(SimpleEnumType::NAME);
    }

    public function testConvertToDatabaseValue()
    {
        $databaseValue = $this->type->convertToDatabaseValue(SimpleEnum::create(SimpleEnum::FIRST), $this->platform);
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
        $this->assertEquals(SimpleEnum::create(SimpleEnum::FIRST), $PHPValue);
    }

    public function testConvertToPHPValueOnNull()
    {
        $PHPValue = $this->type->convertToPHPValue(null, $this->platform);
        $this->assertEquals(SimpleEnum::create(SimpleEnum::ZERO), $PHPValue);
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
