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

trait ExtrasTrait
{
    use EnumCaseAttributesTrait;

    public function getExtra(string $key, bool $throwOnMissingExtra = false): mixed
    {
        if ($throwOnMissingExtra && !isset(static::arrayAccessibleExtras()[$this][$key])) {
            throw new \InvalidArgumentException(sprintf(
                'No value for extra "%s" for enum case %s::%s',
                $key,
                __CLASS__,
                $this->name,
            ));
        }

        return static::arrayAccessibleExtras()[$this][$key] ?? null;
    }

    /**
     * @return iterable<static, mixed>
     */
    public static function extras(string $key, bool $throwOnMissingExtra = false): iterable
    {
        /** @var static $case */
        foreach (static::cases() as $case) {
            yield $case => $case->getExtra($key, $throwOnMissingExtra);
        }
    }

    /**
     * @internal
     */
    private static function arrayAccessibleExtras(): \SplObjectStorage
    {
        static $extras;

        if (!isset($extras)) {
            $extras = new \SplObjectStorage();

            /** @var static $case */
            foreach (static::cases() as $case) {
                $extras[$case] = $case->getEnumCaseAttribute()?->extras;
            }
        }

        return $extras;
    }
}
