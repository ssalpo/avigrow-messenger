<?php

namespace App;

enum OrderStatus: int
{
    case CANCEL = 1;

    public static function labels(): array
    {
        return [
            self::CANCEL->value => 'Отменен'
        ];
    }
}
