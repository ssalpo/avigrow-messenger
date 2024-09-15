<?php

namespace App;

enum TransactionType: int
{
    case DEBIT = 1;

    case CREDIT = 2;
}
