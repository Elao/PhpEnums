<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) 2016 Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Tests\Fixtures\Enum\InvalidFlagsEnum;
use Elao\Enum\Tests\Fixtures\Enum\Permissions;

class FlaggedEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage "1" is not an acceptable value
     */
    public function testThrowExceptionWhenValueIsNotInteger()
    {
        Permissions::accepts('1');
    }

    public function acceptableValueProvider()
    {
        return [
            [Permissions::NONE, true],
            [Permissions::EXECUTE, true],
            [Permissions::WRITE, true],
            [Permissions::READ, true],
            [Permissions::READ | Permissions::WRITE, true],
            [Permissions::ALL, true],
            [99, false],
        ];
    }

    /**
     * @dataProvider acceptableValueProvider
     */
    public function testAcceptableValue($value, $result)
    {
        $this->assertSame(
            $result,
            Permissions::accepts($value),
            sprintf('->accepts() returns %s if the value %d.', $result ? 'true' : 'false', $value)
        );
    }

    /**
     * @expectedException \Elao\Enum\Exception\LogicException
     * @expectedExceptionMessage Possible value 3 of the enumeration "Elao\Enum\Tests\Fixtures\Enum\InvalidFlagsEnum" is not a bit flag.
     */
    public function testThrowExceptionWhenBitmaskIsInvalid()
    {
        InvalidFlagsEnum::create(InvalidFlagsEnum::FIRST);
    }

    public function testSameEnumValueActsAsSingleton()
    {
        $this->assertTrue(Permissions::create(Permissions::NONE) === Permissions::create(Permissions::NONE));
        $this->assertTrue(Permissions::create(Permissions::READ) === Permissions::create(Permissions::READ));
        $all = Permissions::create(Permissions::ALL);
        $this->assertTrue($all === Permissions::create(Permissions::READ | Permissions::WRITE | Permissions::EXECUTE));
        $this->assertTrue(
            $all->removeFlags(Permissions::READ) === Permissions::create(
                Permissions::WRITE | Permissions::EXECUTE
            )
        );
    }

    public function testGetFlagsOfValue()
    {
        $value = Permissions::create(Permissions::NONE | Permissions::WRITE | Permissions::READ);

        $this->assertSame([Permissions::WRITE, Permissions::READ], $value->getFlags());
    }

    public function testSingleFlagIsReadable()
    {
        $this->assertSame('Execute', Permissions::readableFor(Permissions::EXECUTE));

        $instance = Permissions::create(Permissions::EXECUTE);

        $this->assertSame('Execute', $instance->getReadable());
    }

    public function testMultipleFlagsAreReadable()
    {
        $this->assertSame(
            'Execute; Write',
            Permissions::readableFor(Permissions::EXECUTE | Permissions::WRITE)
        );

        $instance = Permissions::create(Permissions::EXECUTE | Permissions::WRITE);

        $this->assertSame('Execute; Write', $instance->getReadable());
    }

    public function testFlagsCombinationCanHaveOwnReadable()
    {
        $this->assertSame(
            'Read & write',
            Permissions::readableFor(Permissions::READ | Permissions::WRITE)
        );

        $instance = Permissions::create(Permissions::ALL);

        $this->assertSame('All permissions', $instance->getReadable());
    }

    public function testNoneCanBeReadabled()
    {
        $this->assertSame('None', Permissions::readableFor(Permissions::NONE));

        $instance = Permissions::create(Permissions::NONE);

        $this->assertSame('None', $instance->getReadable());
    }

    public function testReadableSeparatorCanBeChanged()
    {
        $this->assertSame(
            'Execute | Write',
            Permissions::readableFor(Permissions::EXECUTE | Permissions::WRITE, ' | ')
        );
        $instance = Permissions::create(Permissions::EXECUTE | Permissions::WRITE);
        $this->assertSame('Execute | Write', $instance->getReadable(' | '));
    }

    public function testAddFlags()
    {
        $original = Permissions::create(Permissions::READ);
        $result = $original->addFlags(Permissions::WRITE | Permissions::EXECUTE);

        $this->assertNotSame($original, $result);
        $this->assertTrue($result->hasFlag(Permissions::EXECUTE));
        $this->assertTrue($result->hasFlag(Permissions::WRITE));
        $this->assertTrue($result->hasFlag(Permissions::READ));
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage 8 is not an acceptable value
     */
    public function testThrowExceptionWhenInvalidFlagsAdded()
    {
        $value = Permissions::create(Permissions::READ);
        $value->addFlags(Permissions::ALL + 1);
    }

    public function testRemoveFlags()
    {
        $original = Permissions::create(Permissions::ALL);
        $result = $original->removeFlags(Permissions::READ | Permissions::WRITE);

        $this->assertNotSame($original, $result);
        $this->assertTrue($result->hasFlag(Permissions::EXECUTE));
        $this->assertFalse($result->hasFlag(Permissions::READ));
        $this->assertFalse($result->hasFlag(Permissions::WRITE));
    }

    public function testRemoveAllFlags()
    {
        $original = Permissions::create(Permissions::ALL);
        $result = $original->removeFlags(Permissions::ALL);
        $this->assertCount(0, $result->getFlags());
        $this->assertSame(Permissions::NONE, $result->getValue());
    }

    /**
     * @expectedException \Elao\Enum\Exception\InvalidValueException
     * @expectedExceptionMessage 99 is not an acceptable value
     */
    public function testThrowExceptionWhenInvalidFlagsRemoved()
    {
        $value = Permissions::create(Permissions::ALL);
        $value->removeFlags(99);
    }

    public function testInstances()
    {
        $this->assertSame([
            Permissions::create(Permissions::EXECUTE),
            Permissions::create(Permissions::WRITE),
            Permissions::create(Permissions::READ),
        ], Permissions::instances());
    }
}
