<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Doctrine\ODM\Types;

use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Doctrine\ODM\MongoDB\Types\Type;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types\RequestStatusType;
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types\SuitEnumType;
use Elao\Enum\Tests\Fixtures\Enum\RequestStatus;
use Elao\Enum\Tests\Fixtures\Enum\Suit;
use PHPUnit\Framework\SkippedTestSuiteError;
use PHPUnit\Framework\TestCase;

class EnumTypeTest extends TestCase
{
    /** @var SuitEnumType */
    private $stringType;

    /** @var RequestStatusType */
    private $intType;

    public static function setUpBeforeClass(): void
    {
        if (!class_exists(DoctrineMongoDBBundle::class)) {
            throw new SkippedTestSuiteError('Doctrine MongoDB ODM bundle not installed');
        }

        if (Type::hasType(Suit::class)) {
            Type::overrideType(Suit::class, SuitEnumType::class);
        } else {
            Type::addType(Suit::class, SuitEnumType::class);
        }

        if (Type::hasType(RequestStatus::class)) {
            Type::overrideType(RequestStatus::class, RequestStatusType::class);
        } else {
            Type::addType(RequestStatus::class, RequestStatusType::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->stringType = Type::getType(Suit::class);
        $this->intType = Type::getType(RequestStatus::class);
    }

    public function testConvertToDatabaseValue(): void
    {
        self::assertSame('H', $this->stringType->convertToDatabaseValue(Suit::Hearts));
        self::assertSame(403, $this->intType->convertToDatabaseValue(RequestStatus::Forbidden));
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        self::assertNull($this->stringType->convertToDatabaseValue(null));
        self::assertNull($this->intType->convertToDatabaseValue(null));
    }

    public function testConvertToPHPValue(): void
    {
        self::assertSame(Suit::Hearts, $this->stringType->convertToPHPValue('H'));
        self::assertSame(RequestStatus::Forbidden, $this->intType->convertToPHPValue(403));
    }

    public function testConvertToPHPValueOnNull(): void
    {
        self::assertNull($this->stringType->convertToPHPValue(null));
        self::assertNull($this->intType->convertToPHPValue(null));
    }

    public function testConvertToPHPValueOnInvalidType(): void
    {
        $this->expectException(\TypeError::class);
        $this->stringType->convertToPHPValue(1);
    }

    public function testConvertToPHPValueOnInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        $this->stringType->convertToPHPValue('invalid');
    }
}
