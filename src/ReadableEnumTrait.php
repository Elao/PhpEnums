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
use Elao\Enum\Attribute\ReadableEnum;
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

        return $map[$name]->getReadable();
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
     * Implements readables using PHP 8 attributes, expecting an {@link EnumCase} on each case, with a label,
     * or uses the value as label if {@link ReadableEnum} is used on the class.
     */
    public static function readables(): iterable
    {
        static $readables;

        if (!isset($readables)) {
            $readableEnumAttribute = static::getReadableEnumAttribute();
            $readables = new \SplObjectStorage();
            $r = new \ReflectionEnum(static::class);

            if (($readableEnumAttribute?->useValueAsDefault ?? false) && 'string' !== (string) $r->getBackingType()) {
                throw new LogicException(sprintf(
                    'Cannot use "useValueAsDefault" with "#[%s]" attribute on enum "%s" as it\'s not a string backed enum.',
                    ReadableEnum::class,
                    static::class,
                ));
            }

            /** @var static $case */
            foreach (static::cases() as $case) {
                $attribute = $case->getEnumCaseAttribute();

                if (null === $attribute && null === $readableEnumAttribute) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a "%s" attribute on every cases. Case "%s" is missing one. Alternatively, override the "%s()" method, or use the "%s" attribute on the enum class to use the value as default.',
                        static::class,
                        ReadableEnumTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                        ReadableEnum::class,
                    ));
                }

                if (null === $attribute?->label && null === $readableEnumAttribute) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a label using the "%s" attribute on every cases. Case "%s" is missing a label. Alternatively, override the "%s()" method, or use the "#[%s]" attribute on the enum class to use the value as default.',
                        static::class,
                        ReadableEnumTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                        ReadableEnum::class,
                    ));
                }

                $readables[$case] = sprintf(
                    '%s%s%s',
                    $readableEnumAttribute?->prefix,
                    $attribute?->label ?? ($readableEnumAttribute->useValueAsDefault ? $case->value : $case->name),
                    $readableEnumAttribute?->suffix,
                );
            }
        }

        /** @var static $case */
        foreach (static::cases() as $case) {
            yield $case => $readables[$case];
        }
    }

    /**
     * @internal
     */
    private static function getReadableEnumAttribute(): ?ReadableEnum
    {
        $r = new \ReflectionEnum(static::class);

        if (null === $rAttr = $r->getAttributes(ReadableEnum::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
            return null;
        }

        return $rAttr->newInstance();
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
