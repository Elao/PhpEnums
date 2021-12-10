<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\Type;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\RequestStatusType;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\SuitEnumType;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\DBAL\Types\SuitEnumTypeWithDefaultOnNull;
use Elao\Enum\Tests\Fixtures\Enum\RequestStatus;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\TestCase;

class EnumTypeTest extends TestCase
{
    /** @var AbstractPlatform */
    private $platform;

    /** @var SuitEnumType */
    private $stringType;

    /** @var RequestStatusType */
    private $intType;

    /** @var SuitEnumTypeWithDefaultOnNull */
    private $withDefaultType;

    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(Suit::class)) {
            Type::addType(Suit::class, SuitEnumType::class);
        }

        if (!Type::hasType(RequestStatus::class)) {
            Type::addType(RequestStatus::class, RequestStatusType::class);
        }

        if (!Type::hasType(SuitEnumTypeWithDefaultOnNull::NAME)) {
            Type::addType(SuitEnumTypeWithDefaultOnNull::NAME, SuitEnumTypeWithDefaultOnNull::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->platform = new MySQLPlatform();
        $this->stringType = Type::getType(Suit::class);
        $this->intType = Type::getType(RequestStatus::class);
        $this->withDefaultType = Type::getType(SuitEnumTypeWithDefaultOnNull::NAME);
    }

    public function testDefaultNameIsEnumClass(): void
    {
        self::assertSame(Suit::class, $this->stringType->getName(), 'default name is the enum FQCN');
        self::assertSame(RequestStatus::class, $this->intType->getName(), 'default name is the enum FQCN');
        self::assertSame(SuitEnumTypeWithDefaultOnNull::NAME, $this->withDefaultType->getName(), 'default name can be overridden');
    }

    public function testConvertToDatabaseValue(): void
    {
        $databaseValue = $this->stringType->convertToDatabaseValue(Suit::Hearts, $this->platform);
        self::assertSame('H', $databaseValue);

        $databaseValue = $this->intType->convertToDatabaseValue(RequestStatus::Forbidden, $this->platform);
        self::assertSame(403, $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        $databaseValue = $this->stringType->convertToDatabaseValue(null, $this->platform);
        self::assertNull($databaseValue);

        $databaseValue = $this->withDefaultType->convertToDatabaseValue(null, $this->platform);
        self::assertSame('S', $databaseValue);
    }

    public function testConvertToPHPValue(): void
    {
        $PHPValue = $this->stringType->convertToPHPValue('H', $this->platform);
        self::assertSame(Suit::Hearts, $PHPValue);

        $PHPValue = $this->intType->convertToPHPValue(403, $this->platform);
        self::assertSame(RequestStatus::Forbidden, $PHPValue);
    }

    public function testConvertToPHPValueOnNull(): void
    {
        $PHPValue = $this->stringType->convertToPHPValue(null, $this->platform);
        self::assertNull($PHPValue);

        $PHPValue = $this->withDefaultType->convertToPHPValue(null, $this->platform);
        self::assertSame(Suit::Spades, $PHPValue);
    }

    public function testRequiresSQLCommentHintIsTrue(): void
    {
        self::assertTrue($this->stringType->requiresSQLCommentHint($this->platform));
        self::assertTrue($this->intType->requiresSQLCommentHint($this->platform));
        self::assertTrue($this->withDefaultType->requiresSQLCommentHint($this->platform));
    }

    public function testIntegerBindingType(): void
    {
        self::assertSame(ParameterType::STRING, $this->stringType->getBindingType());
        self::assertSame(ParameterType::STRING, $this->withDefaultType->getBindingType());
        self::assertSame(ParameterType::INTEGER, $this->intType->getBindingType());
    }
}
