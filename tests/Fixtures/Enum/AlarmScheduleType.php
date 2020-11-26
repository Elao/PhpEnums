<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum\Tests\Fixtures\Enum;

use Elao\Enum\FlaggedEnum;

final class AlarmScheduleType extends FlaggedEnum
{
    public const MONDAY_MORNING = 1 << 0;
    public const MONDAY_AFTERNOON = 1 << 1;
    public const TUESDAY_MORNING = 1 << 2;
    public const TUESDAY_AFTERNOON = 1 << 3;
    public const WEDNESDAY_MORNING = 1 << 4;
    public const WEDNESDAY_AFTERNOON = 1 << 5;
    public const THURSDAY_MORNING = 1 << 6;
    public const THURSDAY_AFTERNOON = 1 << 7;
    public const FRIDAY_MORNING = 1 << 8;
    public const FRIDAY_AFTERNOON = 1 << 9;
    public const SATURDAY_MORNING = 1 << 10;
    public const SATURDAY_AFTERNOON = 1 << 11;
    public const SUNDAY_MORNING = 1 << 12;
    public const SUNDAY_AFTERNOON = 1 << 13;

    /**
     * {@inheritdoc}
     */
    public static function values(): array
    {
        return [
            static::MONDAY_MORNING,
            static::MONDAY_AFTERNOON,
            static::TUESDAY_MORNING,
            static::TUESDAY_AFTERNOON,
            static::WEDNESDAY_MORNING,
            static::WEDNESDAY_AFTERNOON,
            static::THURSDAY_MORNING,
            static::THURSDAY_AFTERNOON,
            static::FRIDAY_MORNING,
            static::FRIDAY_AFTERNOON,
            static::SATURDAY_MORNING,
            static::SATURDAY_AFTERNOON,
            static::SUNDAY_MORNING,
            static::SUNDAY_AFTERNOON,
        ];
    }
}
