<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Unit;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Exception\LogicException;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;
use PHPUnit\Framework\TestCase;

class ReadableEnumAttributeTest extends TestCase
{
    public function testReadableEnumAttribute(): void
    {
        self::assertSame(
            'suit.Clubs.label',
            ReadableAttributeSuit::readableForValue(ReadableAttributeSuit::Clubs->value),
            'uses the name as default, with suffix and prefix',
        );
        self::assertSame(
            'suit.hearts.label',
            ReadableAttributeSuit::readableForValue(ReadableAttributeSuit::Hearts->value),
            'uses the explicit label value, with suffix and prefix',
        );
    }

    public function testReadableEnumAttributeWithoutSuffixPrefix(): void
    {
        self::assertSame(
            'Clubs',
            ReadableAttributeSuitWithoutSuffixPrefix::readableForValue(ReadableAttributeSuitWithoutSuffixPrefix::Clubs->value),
            'uses the name as default, without any suffix or prefix',
        );
        self::assertSame(
            'hearts',
            ReadableAttributeSuitWithoutSuffixPrefix::readableForValue(ReadableAttributeSuitWithoutSuffixPrefix::Hearts->value),
            'uses the explicit label value, without any suffix or prefix',
        );
    }

    public function testReadableEnumAttributeWithValueAsDefault(): void
    {
        self::assertSame(
            'suit.♣︎.label',
            ReadableSuitAttributeWithValueAsDefault::readableForValue(ReadableSuitAttributeWithValueAsDefault::Clubs->value),
            'uses the value as default, with suffix and prefix',
        );
        self::assertSame(
            'suit.hearts.label',
            ReadableSuitAttributeWithValueAsDefault::readableForValue(ReadableSuitAttributeWithValueAsDefault::Hearts->value),
            'uses the explicit label value, with suffix and prefix',
        );
    }

    public function testReadableEnumAttributeWithValueAsDefaultThrowsOnPureEnum(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot use "useValueAsDefault" with "#[Elao\Enum\Attribute\ReadableEnum]" attribute on enum "Elao\Enum\Tests\Unit\PureEnumWithReadableAttribute" as it\'s not a string backed enum.');

        PureEnumWithReadableAttribute::Foo->getReadable();
    }

    public function testReadableEnumAttributeWithValueAsDefaultThrowsOnIntBackedEnum(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot use "useValueAsDefault" with "#[Elao\Enum\Attribute\ReadableEnum]" attribute on enum "Elao\Enum\Tests\Unit\IntBackedEnumWithReadableAttribute" as it\'s not a string backed enum.');

        IntBackedEnumWithReadableAttribute::Foo->getReadable();
    }
}

#[ReadableEnum(prefix: 'suit.', suffix: '.label')]
enum ReadableAttributeSuit: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('hearts')]
    case Hearts = '♥︎';

    case Diamonds = '♦︎';
    case Clubs = '♣︎';
    case Spades = '︎♠︎';
}

#[ReadableEnum]
enum ReadableAttributeSuitWithoutSuffixPrefix: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('hearts')]
    case Hearts = '♥︎';

    case Diamonds = '♦︎';
    case Clubs = '♣︎';
    case Spades = '︎♠︎';
}

#[ReadableEnum(prefix: 'suit.', suffix: '.label', useValueAsDefault: true)]
enum ReadableSuitAttributeWithValueAsDefault: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('hearts')]
    case Hearts = '♥︎';

    case Diamonds = '♦︎';
    case Clubs = '♣︎';
    case Spades = '︎♠︎';
}

#[ReadableEnum(useValueAsDefault: true)]
enum PureEnumWithReadableAttribute implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    case Foo;
}

#[ReadableEnum(useValueAsDefault: true)]
enum IntBackedEnumWithReadableAttribute: int implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    case Foo = 1;
}
