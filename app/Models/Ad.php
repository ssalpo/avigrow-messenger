<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ad extends Model
{
    protected $fillable = [
        'account_id',
        'external_id',
        'price',
        'title',
        'url',
        'bot_id'
    ];

    protected $hidden = [
        'bot_id'
    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }
}
