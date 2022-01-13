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

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Exception\LogicException;
use Elao\Enum\Exception\NameException;

trait ReadableEnumTrait
{
    use EnumCaseAttributesTrait;

    /**
     * {@inheritdoc}
     */
    public static function readableForValue(string|int $value): string
    {
        if (!is_a(static::class, \BackedEnum::class, true)) {
            throw new \BadMethodCallException(sprintf(
                'Cannot call method "%s" on non-backed enum "%s".',
                __METHOD__,
                static::class,
            ));
        }

        /** @var ReadableEnumInterface $case */
        $case = static::from($value);

        return $case->getReadable();
    }

    /**
     * {@inheritdoc}
     */
    public static function readableForName(string $name): string
    {
        /** @var array<string,ReadableEnumInterface>|null $map */
        static $map;

        if (null === $map) {
            $map = array_combine(array_map(static fn (\UnitEnum $e) => $e->name, static::cases()), static::cases());
        }

        if (!isset($map[$name])) {
            throw new NameException($name, static::class);
        }

        return ($map[$name])->getReadable();
    }

    /**
     * {@inheritdoc}
     */
    public function getReadable(): string
    {
        return static::arrayAccessReadables()[$this];
    }

    /**
     * {@inheritdoc}
     *
     * Implements readables using PHP 8 attributes, expecting an {@link EnumCase} on each case, with a label.
     */
    public static function readables(): iterable
    {
        static $readables;

        if (!isset($readables)) {
            $readables = new \SplObjectStorage();

            /** @var static $case */
            foreach (static::cases() as $case) {
                $attribute = $case->getEnumCaseAttribute();

                if (null === $attribute) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a "%s" attribute on every cases. Case "%s" is missing one. Alternatively, override the "%s()" method',
                        static::class,
                        ReadableEnumTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                    ));
                }

                if (null === $attribute->label) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a label using the "%s" attribute on every cases. Case "%s" is missing a label. Alternatively, override the "%s()" method',
                        static::class,
                        ReadableEnumTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                    ));
                }

                $readables[$case] = $attribute->label;
            }
        }

        /** @var static $case */
        foreach (static::cases() as $case) {
            yield $case => $readables[$case];
        }
    }

    /**
     * As objects, {@link https://wiki.php.net/rfc/enumerations#splobjectstorage_and_weakmaps Enum cases cannot be used as keys in an array}.
     * However, they can be used as keys in a SplObjectStorage or WeakMap.
     * Because they are singletons they never get garbage collected, and thus will never be removed from a WeakMap,
     * making these two storage mechanisms effectively equivalent.
     *
     * However, there is a {@link https://wiki.php.net/rfc/object_keys_in_arrays pending RFC} regarding object as array keys.
     *
     * @internal
     */
    private static function arrayAccessReadables(): \SplObjectStorage
    {
        static $readables;

        if (!isset($readables)) {
            $readables = new \SplObjectStorage();
            foreach (static::readables() as $case => $label) {
                $readables[$case] = $label;
            }
        }

        return $readables;
    }
}
