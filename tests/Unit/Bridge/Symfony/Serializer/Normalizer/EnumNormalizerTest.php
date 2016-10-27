<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Symfony\Serializer\Normalizer;

use Elao\Enum\Bridge\Symfony\Serializer\Normalizer\EnumNormalizer;
use Elao\Enum\Tests\Fixtures\Unit\EnumTest\Gender;

class EnumNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var EnumNormalizer */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = new EnumNormalizer();
    }

    public function testSupportsNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(Gender::create(Gender::MALE)));
    }

    public function testsNormalize()
    {
        $this->assertSame(Gender::MALE, $this->normalizer->normalize(Gender::create(Gender::MALE)));
    }

    public function testSupportsDenormalization()
    {
        $this->assertTrue($this->normalizer->supportsDenormalization((string) Gender::MALE, Gender::class));
    }

    public function testsDenormalize()
    {
        $this->assertEquals(
            Gender::create(Gender::MALE),
            $this->normalizer->denormalize((string) Gender::MALE, Gender::class)
        );
    }

    /**
     * @expectedException \Symfony\Component\Serializer\Exception\UnexpectedValueException
     */
    public function testsDenormalizeWithWrongValueThrowsException()
    {
        $this->assertEquals(
            Gender::create(Gender::MALE),
            $this->normalizer->denormalize('invalid_data', Gender::class)
        );
    }
}
