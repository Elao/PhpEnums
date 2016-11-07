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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\GenderEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;

class EnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var AbstractPlatform */
    protected $platform;

    /** @var GenderEnumType */
    protected $type;

    public static function setUpBeforeClass()
    {
        Type::addType(GenderEnumType::NAME, GenderEnumType::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->platform = $this->prophesize(AbstractPlatform::class)->reveal();
        $this->type = Type::getType(GenderEnumType::NAME);
    }

    public function testConvertToDatabaseValue()
    {
        $databaseValue = $this->type->convertToDatabaseValue(Gender::create(Gender::MALE), $this->platform);
        $this->assertSame('male', $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull()
    {
        $databaseValue = $this->type->convertToDatabaseValue(null, $this->platform);
        $this->assertSame('unknown', $databaseValue);
    }

    public function testConvertToPHPValue()
    {
        $PHPValue = $this->type->convertToPHPValue('male', $this->platform);
        $this->assertSame(Gender::create(Gender::MALE), $PHPValue);
    }

    public function testConvertToPHPValueOnNull()
    {
        $PHPValue = $this->type->convertToPHPValue(null, $this->platform);
        $this->assertSame(Gender::create(Gender::UNKNOW), $PHPValue);
    }

    public function testRequiresSQLCommentHintIsTrue()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testIntegerBindingType()
    {
        $this->assertSame(\PDO::PARAM_STR, $this->type->getBindingType());
    }
}
