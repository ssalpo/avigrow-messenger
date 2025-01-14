<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'account_id',
        'external_id',
        'external_created_at',
        'content',
        'item_id',
        'item_title',
        'sender',
    ];
}
