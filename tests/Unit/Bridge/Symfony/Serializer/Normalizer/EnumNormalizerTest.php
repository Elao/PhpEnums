<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Serializer\Normalizer;

use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\TestCase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class EnumNormalizerTest extends TestCase
{
    /** @var EnumNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new EnumNormalizer();
    }

    public function testSupportsNormalization(): void
    {
        self::assertTrue($this->normalizer->supportsNormalization(Gender::get(Gender::MALE)));
    }

    public function testsNormalize(): void
    {
        self::assertSame(Gender::MALE, $this->normalizer->normalize(Gender::get(Gender::MALE)));
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->normalizer->supportsDenormalization(Gender::MALE, Gender::class));
    }

    public function testsDenormalize(): void
    {
        self::assertSame(
            Gender::get(Gender::MALE),
            $this->normalizer->denormalize(Gender::MALE, Gender::class)
        );
    }

    public function testsDenormalizeWithWrongValueThrowsException(): void
    {
        $this->expectException(UnexpectedValueException::class);

        self::assertSame(
            Gender::get(Gender::MALE),
            $this->normalizer->denormalize('invalid_data', Gender::class)
        );
    }
}
