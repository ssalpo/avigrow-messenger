<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'comment',
        'order_id',
        'type'
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    protected $appends = [
        'created_at_formatted',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->created_at->format('Y-m-d H:i'),
        );
    }

    public function scopeDebit($q): void
    {
        $q->where('type', TransactionType::DEBIT->value);
    }

    public function scopeCredit($q): void
    {
        $q->where('type', TransactionType::CREDIT->value);
    }
}
