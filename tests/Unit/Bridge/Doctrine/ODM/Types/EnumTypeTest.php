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
use Elao\Enum\Tests\Fixtures\Bridge\Doctrine\ODM\Types\GenderEnumType;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\TestCase;

class EnumTypeTest extends TestCase
{
    /** @var GenderEnumType */
    protected $type;
    protected const NAME = 'gender';

    public static function setUpBeforeClass(): void
    {
        if (!class_exists(Type::class)) {
            self::markTestSkipped('Doctrine MongoDB ODM not installed');
        }

        if (!Type::hasType(self::NAME)) {
            Type::addType(self::NAME, GenderEnumType::class);
        }
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
        $databaseValue = $this->type->convertToDatabaseValue(Gender::get(Gender::MALE));
        self::assertSame('male', $databaseValue);
    }

    public function testConvertToDatabaseValueOnNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null));
    }

    public function testConvertToPHPValue(): void
    {
        $PHPValue = $this->type->convertToPHPValue('male');
        self::assertSame(Gender::get(Gender::MALE), $PHPValue);
    }

    public function testConvertToPHPValueOnNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null));
    }
}
