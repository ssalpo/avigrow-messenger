<?php

namespace App\Enums;

use App\Enums\Traits\EnumToCollection;

enum AccountType: int
{
    use EnumToCollection;

    case FREE = 0;

    case PRO = 1;

    public function isPro(): bool
    {
        return $this->value === self::PRO->value;
    }

    public function isFree(): bool
    {
        return $this->value === self::FREE->value;
    }
}
