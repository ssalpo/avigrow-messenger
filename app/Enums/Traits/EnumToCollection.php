<?php

namespace App\Enums\Traits;

use Illuminate\Support\Collection;

trait EnumToCollection
{
    public static function names(): Collection
    {
        return collect(self::cases())
            ->pluck('name')
            ->map(fn ($e) => mb_strtolower($e));
    }

    public static function values(): Collection
    {
        return collect(self::cases())->pluck('value');
    }

    public static function all(): Collection
    {
        return collect(self::values())->combine(self::names());
    }
}
