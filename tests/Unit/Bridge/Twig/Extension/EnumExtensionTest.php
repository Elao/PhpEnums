<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit\Bridge\Twig\Extension;

use Elao\Enum\Bridge\Twig\Extension\EnumExtension;
use Elao\Enum\Exception\InvalidArgumentException;
use Elao\Enum\Tests\Fixtures\Enum\Gender;
use Elao\Enum\Tests\TestCase;
use Twig\TwigFunction;

class EnumExtensionTest extends TestCase
{
    /** @var EnumExtension */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new EnumExtension();
    }

    public function test enum_get(): void
    {
        self::assertSame(
            Gender::FEMALE(),
            $this->getFunction('enum_get')->getCallable()(Gender::class, Gender::FEMALE),
        );
    }

    public function test enum_get with invalid class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not an "Elao\Enum\EnumInterface"');

        $this->getFunction('enum_get')->getCallable()(\stdClass::class, Gender::FEMALE);
    }

    public function test enum_values(): void
    {
        self::assertSame(Gender::values(), $this->getFunction('enum_values')->getCallable()(Gender::class));
    }

    public function test enum_values with invalid class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not an "Elao\Enum\EnumInterface"');

        self::assertSame(Gender::values(), $this->getFunction('enum_values')->getCallable()(\stdClass::class));
    }

    public function test enum_accepts(): void
    {
        self::assertTrue($this->getFunction('enum_accepts')->getCallable()(Gender::class, Gender::FEMALE));
        self::assertFalse($this->getFunction('enum_accepts')->getCallable()(Gender::class, 'not a valid value'));
    }

    public function test enum_accepts with invalid class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not an "Elao\Enum\EnumInterface"');

        self::assertSame(Gender::values(), $this->getFunction('enum_accepts')->getCallable()(\stdClass::class, Gender::FEMALE));
    }

    public function test enum_instances(): void
    {
        self::assertSame(Gender::instances(), $this->getFunction('enum_instances')->getCallable()(Gender::class));
    }

    public function test enum_instances with invalid class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not an "Elao\Enum\EnumInterface"');

        self::assertSame(Gender::values(), $this->getFunction('enum_instances')->getCallable()(\stdClass::class));
    }

    public function test enum_readables(): void
    {
        self::assertSame(Gender::readables(), $this->getFunction('enum_readables')->getCallable()(Gender::class));
    }

    public function test enum_readables with invalid class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not a "Elao\Enum\ReadableEnumInterface"');

        self::assertSame(Gender::values(), $this->getFunction('enum_readables')->getCallable()(\stdClass::class));
    }

    public function test enum_readable_for(): void
    {
        self::assertSame(Gender::FEMALE()->getReadable(), $this->getFunction('enum_readable_for')->getCallable()(Gender::class, Gender::FEMALE));
    }

    public function test enum_readable_for with invalid class(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"stdClass" is not a "Elao\Enum\ReadableEnumInterface"');

        self::assertSame(Gender::values(), $this->getFunction('enum_readable_for')->getCallable()(\stdClass::class, Gender::FEMALE));
    }

    private function getFunction(string $method): TwigFunction
    {
        foreach ($this->extension->getFunctions() as $function) {
            if ($method === $function->getName()) {
                return $function;
            }
        }

        self::fail("Twig function \"$method\" is not registered");
    }
}
