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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\GenderEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\TestCase;

class EnumTypeTest extends TestCase
{
    /** @var AbstractPlatform */
    protected $platform;

    /** @var GenderEnumType */
    protected $type;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(GenderEnumType::NAME)) {
            Type::addType(GenderEnumType::NAME, GenderEnumType::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->platform = $this->prophesize(AbstractPlatform::class)->reveal();
        $this->type = Type::getType(GenderEnumType::NAME);
    }

    public function testConvertToDatabaseValue(): void
    {
        $databaseValue = $this->type->convertToDatabaseValue(Gender::get(Gender::MALE), $this->platform);
        self::assertSame('male', $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        $databaseValue = $this->type->convertToDatabaseValue(null, $this->platform);
        self::assertSame('unknown', $databaseValue);
    }

    public function testConvertToPHPValue(): void
    {
        $PHPValue = $this->type->convertToPHPValue('male', $this->platform);
        self::assertSame(Gender::get(Gender::MALE), $PHPValue);
    }

    public function testConvertToPHPValueOnNull(): void
    {
        $PHPValue = $this->type->convertToPHPValue(null, $this->platform);
        self::assertSame(Gender::get(Gender::UNKNOW), $PHPValue);
    }

    public function testRequiresSQLCommentHintIsTrue(): void
    {
        self::assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testIntegerBindingType(): void
    {
        self::assertSame(\PDO::PARAM_STR, $this->type->getBindingType());
    }
}
