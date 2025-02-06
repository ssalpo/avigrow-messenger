<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountStatus extends Model
{
    protected $fillable = [
        'account_id',
        'is_enabled',
        'always_online',
        'available_from',
        'available_to',
    ];

    protected $casts = [
        'is_enabled' => 'bool',
        'always_online' => 'bool',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_enabled', true);
    }
}
