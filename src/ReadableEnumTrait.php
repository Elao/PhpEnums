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
    /**
     * {@inheritdoc}
     */
    public static function readableForValue(string|int $value): string
    {
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
        return static::cachedReadables()[$this];
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
            $r = new \ReflectionEnum(static::class);

            foreach (static::cases() as $case) {
                $rCase = $r->getCase($case->name);
                if (null === $rAttr = $rCase->getAttributes(EnumCase::class)[0] ?? null) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a "%s" attribute on every cases. Case "%s" is missing one. Alternatively, override the "%s()" method',
                        static::class,
                        ReadableEnumTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                    ));
                }

                /** @var EnumCase $attr */
                $attr = $rAttr->newInstance();

                if (null === $attr->label) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a label using the "%s" attribute on every cases. Case "%s" is missing a label. Alternatively, override the "%s()" method',
                        static::class,
                        ReadableEnumTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                    ));
                }

                $readables[$case] = $attr->label;
            }
        }

        return $readables;
    }

    /**
     * As objects, {@link https://wiki.php.net/rfc/enumerations#splobjectstorage_and_weakmaps Enum cases cannot be used as keys in an array}.
     * However, they can be used as keys in a SplObjectStorage or WeakMap.
     * Because they are singletons they never get garbage collected, and thus will never be removed from a WeakMap,
     * making these two storage mechanisms effectively equivalent.
     *
     * However, there is a {@link https://wiki.php.net/rfc/object_keys_in_arrays pending RFC} regarding object as array keys.
     */
    private static function cachedReadables(): \ArrayAccess
    {
        static $readables;

        if (!isset($readables)) {
            $readables = static::readables();
            if ($readables instanceof \ArrayAccess) {
                return $readables;
            }

            $readables = new \SplObjectStorage();
            foreach (static::readables() as $case => $label) {
                $readables[$case] = $label;
            }
        }

        return $readables;
    }
}
