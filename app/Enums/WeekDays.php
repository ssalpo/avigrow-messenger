<?php

namespace App\Enums;

use App\Enums\Traits\EnumToCollection;

enum WeekDays: int
{
    use EnumToCollection;

    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;

    public static function isValidDay(int $day): bool
    {
        return self::values()->contains($day);
    }
}
