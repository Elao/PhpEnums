<?php

declare(strict_types=1);

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\InvalidArgumentException;
use Elao\Enum\Exception\LogicException;

/**
 * A bag of {@see \BackedEnum} allowing to perform bit operations.
 *
 * @final
 * @template T of \BackedEnum
 */
class FlagBag
{
    public const NONE = 0;

    /** @var array<class-string<T>, int> */
    private static array $masks = [];

    /** @var int[] */
    private array $bits;

    /** @var class-string<T> */
    private string $type;

    /**
     * @param class-string<T> $enumType
     */
    public function __construct(string $enumType, int ...$bits)
    {
        self::checkIntBackedEnumType($enumType);

        $this->type = $enumType;

        $bits = self::encodeBits($bits);

        if (!static::accepts($enumType, $bits)) {
            throw new InvalidArgumentException(sprintf('%d is not a valid bits combination for "%s"', $bits, $this->type));
        }

        $this->bits = static::decodeBits($bits);
    }

    /**
     * @param class-string $enumType
     */
    private static function checkIntBackedEnumType(string $enumType): void
    {
        if (!is_a($enumType, \BackedEnum::class, true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a backed enum', $enumType));
        }

        if ('int' !== (string) (new \ReflectionEnum($enumType))->getBackingType()) {
            throw new InvalidArgumentException(sprintf('"%s" is not an int backed enum', $enumType));
        }
    }

    /**
     * @param class-string<T> $enumType
     */
    public static function fromAll(string $enumType): FlagBag
    {
        return new FlagBag($enumType, static::getBitmask($enumType));
    }

    /**
     * @param class-string<T>|T $enumOrType
     */
    public static function from(string|\BackedEnum $enumOrType, \BackedEnum ...$flags): static
    {
        if ($enumOrType instanceof \BackedEnum) {
            if ('int' !== (string) (new \ReflectionEnum($enumOrType))->getBackingType()) {
                throw new InvalidArgumentException(sprintf('"%s" is not an int backed enum', $enumOrType::class));
            }

            $type = $enumOrType::class;
            $flags[] = $enumOrType;
        } else {
            if (!is_a($enumOrType, \BackedEnum::class, true)) {
                throw new InvalidArgumentException(sprintf('"%s" is not a backed enum', $enumOrType));
            }

            $type = $enumOrType;
        }

        return new static($type, static::encodeBits(array_map(static fn (\BackedEnum $flag) => $flag->value, $flags)));
    }

    /**
     * @param class-string<T> $enumType
     */
    public static function accepts(string $enumType, int $value): bool
    {
        if ($value === self::NONE) {
            return true;
        }

        return $value === ($value & self::getBitmask($enumType));
    }

    /**
     * E.g: 43 => [1, 2, 8, 32]
     *
     * @return int[]
     */
    private static function decodeBits(int $flags): array
    {
        /** @var int[] $bits */
        $bits = array_reverse(str_split(decbin($flags)));

        return array_values(
            array_filter(
                array_map(
                    static fn ($k, $v) => $v === '1' ? (1 << $k) : null,
                    array_keys($bits),
                    array_values($bits),
                )
            )
        );
    }

    /**
     * E.g: [1, 2, 8, 32] => 43
     *
     * @param int[] $bits
     */
    private static function encodeBits(array $bits): int
    {
        return array_sum(array_unique($bits));
    }

    /**
     * Gets an integer value of the possible flags for enumeration.
     *
     * @param class-string<T> $enumType
     *
     * @throws LogicException If the possibles values are not valid bit flags
     */
    public static function getBitmask(string $enumType): int
    {
        if (!isset(self::$masks[$enumType])) {
            self::checkIntBackedEnumType($enumType);

            /** @var \BackedEnum[] $cases */
            $cases = $enumType::cases();
            $mask = 0;
            foreach ($cases as $case) {
                $value = $case->value;
                if ($value < 1 || ($value > 1 && ($value % 2) !== 0)) {
                    throw new LogicException(sprintf(
                        'Possible value %s of the enumeration "%s" is not a bit flag.',
                        $value,
                        $enumType
                    ));
                }
                $mask |= $value;
            }
            self::$masks[$enumType] = $mask;
        }

        return self::$masks[$enumType];
    }

    /**
     * @return int[]
     */
    public function getBits(): array
    {
        return $this->bits;
    }

    /**
     * The encoded bits value.
     */
    public function getValue(): int
    {
        return self::encodeBits($this->bits);
    }

    /**
     * @return array<T>
     */
    public function getFlags(): array
    {
        return array_map(fn (int $bit) => $this->type::from($bit), $this->bits);
    }

    /**
     * True if the bit flag(s) are also present in the current bag.
     */
    public function hasBits(int $bits): bool
    {
        if ($bits >= 1) {
            return $bits === ($bits & self::encodeBits($this->bits));
        }

        return false;
    }

    /**
     * True if the all the flags are also present in the current bag.
     *
     * @param T ...$flags
     */
    public function hasFlags(\BackedEnum ...$flags): bool
    {
        $bits = static::encodeBits(array_map(static fn (\BackedEnum $flag) => $flag->value, $flags));

        if ($flags >= 1) {
            return $bits === ($bits & self::encodeBits($this->bits));
        }

        return false;
    }

    /**
     * Computes a new value with given flags, and returns the corresponding instance.
     */
    public function withBits(int ...$bits): static
    {
        $mask = self::encodeBits($bits);

        if (!static::accepts($this->type, $mask)) {
            throw new InvalidArgumentException(sprintf('"%d" is not a valid flags combination for "%s"', $mask, $this->type));
        }

        return new static($this->type, self::encodeBits($this->bits) | $mask);
    }

    /**
     * @param T ...$flags
     */
    public function withFlags(\BackedEnum ...$flags): static
    {
        return $this->withBits(...$this->flagsToBits(...$flags));
    }

    /**
     * @param T ...$flags
     */
    public function withoutFlags(\BackedEnum ...$flags): static
    {
        return $this->withoutBits(...$this->flagsToBits(...$flags));
    }

    public function withoutBits(int ...$bits): static
    {
        $mask = self::encodeBits($bits);

        if (!static::accepts($this->type, $mask)) {
            throw new InvalidArgumentException(sprintf('"%d" is not a valid flags combination for "%s"', $mask, $this->type));
        }

        return new static($this->type, self::encodeBits($this->bits) & ~$mask);
    }

    /**
     * @return class-string<T>
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param array<T> $flags
     *
     * @return int[]
     */
    private function flagsToBits(\BackedEnum ...$flags): array
    {
        return array_map(static fn (\BackedEnum $flag) => $flag->value, $flags);
    }
}
