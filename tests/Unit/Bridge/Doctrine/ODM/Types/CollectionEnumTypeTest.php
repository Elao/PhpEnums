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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types\RequestStatusCollectionType;
use Elao\Enum\Tests\Fixtures\Enum\RequestStatus;
use PHPUnit\Framework\TestCase;

class CollectionEnumTypeTest extends TestCase
{
    /** @var RequestStatusCollectionType */
    private $type;
    private const NAME = 'request_statuses';

    public static function setUpBeforeClass(): void
    {
        if (!class_exists(DoctrineMongoDBBundle::class)) {
            self::markTestSkipped('Doctrine MongoDB ODM bundle not installed');
        }

        Type::addType(self::NAME, RequestStatusCollectionType::class);
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
        self::assertSame([403, 500], $this->type->convertToDatabaseValue([
            RequestStatus::Forbidden,
            RequestStatus::InternalServerError,
        ]));
    }

    public function testConvertToDatabaseValueReturnsUniqueValues(): void
    {
        self::assertSame([200, 500], $this->type->convertToDatabaseValue([
            RequestStatus::Success,
            RequestStatus::InternalServerError,
            RequestStatus::Success,
            RequestStatus::InternalServerError,
        ]));
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null));
    }

    public function testConvertToPHPValue(): void
    {
        self::assertSame(
            [RequestStatus::Success, RequestStatus::Forbidden],
            $this->type->convertToPHPValue([200, 403])
        );
    }

    public function testConvertToPHPValueReturnsUniqueValue(): void
    {
        self::assertSame(
            [RequestStatus::Success, RequestStatus::Forbidden],
            $this->type->convertToPHPValue([200, 403, 200, 200])
        );
    }

    public function testConvertToPHPValueOnNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null));
    }

    public function testConvertToPHPValueOnEmptyArray(): void
    {
        self::assertSame([], $this->type->convertToPHPValue([]));
    }

    public function testConvertToPHPValueOnInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        $this->type->convertToPHPValue([301]);
    }
}
