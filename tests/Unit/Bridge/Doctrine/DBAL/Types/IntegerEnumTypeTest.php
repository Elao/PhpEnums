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

    public function testConvertToDatabaseValue(): void
    {
        $databaseValue = $this->type->convertToDatabaseValue(SimpleEnum::get(SimpleEnum::FIRST), $this->platform);
        self::assertSame(1, $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        $databaseValue = $this->type->convertToDatabaseValue(null, $this->platform);
        self::assertSame(0, $databaseValue);
    }

    public function testConvertToPHPValue(): void
    {
        $PHPValue = $this->type->convertToPHPValue(1, $this->platform);
        self::assertSame(SimpleEnum::get(SimpleEnum::FIRST), $PHPValue);
    }

    public function testConvertToPHPValueOnNull(): void
    {
        $PHPValue = $this->type->convertToPHPValue(null, $this->platform);
        self::assertSame(SimpleEnum::get(SimpleEnum::ZERO), $PHPValue);
    }

    public function testRequiresSQLCommentHintIsTrue(): void
    {
        self::assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testIntegerBindingType(): void
    {
        self::assertSame(\PDO::PARAM_INT, $this->type->getBindingType());
    }
}
