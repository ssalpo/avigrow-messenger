<?php

namespace App;

enum CodeKeyType: int
{
    case VD = 1;

    case SHAPR = 2;

    public static function labels(): array
    {
        return [
            self::VD->value => 'VD',
            self::SHAPR->value => 'Shapr',
        ];
    }
}
