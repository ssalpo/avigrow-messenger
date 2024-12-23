<?php

namespace App\Enums;

enum AccountConnectStatus: int
{
    case CONNECTION_PENDING = 1;

    case CONNECTION_ERROR = 2;

    case CONNECTED = 3;

    case DISCONNECTED = 4;
}
