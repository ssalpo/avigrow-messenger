<?php

namespace App\Enums;

enum CodeKeyType: int
{
    case VD = 1;

    case SHAPR = 2;

    case MIRO = 3;

    case JETBRAINS = 4;

    public static function labels(): array
    {
        return [
            self::VD->value => 'VD',
            self::SHAPR->value => 'Shapr',
            self::MIRO->value => 'Miro',
            self::JETBRAINS->value => 'Jetbrains',
        ];
    }
}
