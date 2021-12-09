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
        /** @var \UnitEnum $case */
        $case = static::from($value);

        return (static::readables())[$case->name];
    }

    /**
     * {@inheritdoc}
     */
    public static function readableForName(string $name): string
    {
        self::guardValidName($name);

        return (static::readables())[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getReadable(): string
    {
        return static::readableForValue($this->value);
    }

    public static function readables(): array
    {
        static $readables;

        if (!isset($readables)) {
            $r = new \ReflectionEnum(static::class);

            foreach ($r->getCases() as $case) {
                if (null === $rAttr = $case->getAttributes(EnumCase::class)[0] ?? null) {
                    throw new LogicException(sprintf(
                        'enum "%s" using the "%s" trait must define a "%s" attribute on every cases. Case "%s" is missing one. Alternatively, override the "%s()" method',
                        static::class,
                        ReadableEnumFromAttributesTrait::class,
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
                        ReadableEnumFromAttributesTrait::class,
                        EnumCase::class,
                        $case->name,
                        __METHOD__,
                    ));
                }

                $readables[$case->name] = $attr->label;
            }
        }

        return $readables;
    }

    private static function guardValidName(string $name): void
    {
        /** @var array<string,\UnitEnum>|null $map */
        static $map;

        if (null === $map) {
            $map = array_combine(array_map(fn (\UnitEnum $e) => $e->name, static::cases()), static::cases());
        }

        if (!isset($map[$name])) {
            throw new NameException($name, static::class);
        }
    }
}
