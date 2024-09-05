<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'account_id',
        'send_at',
        'status',
    ];

    public static function hasAnyForChat(string $id): bool
    {
        return self::where(['chat_id' => $id])->exists();
    }

    public static function scopeForAccount($q, $accountId): void
    {
        $q->where('account_id', $accountId);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
