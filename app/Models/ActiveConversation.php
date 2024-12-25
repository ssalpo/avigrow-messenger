<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveConversation extends Model
{
    protected $fillable = [
        'account_id', 'chat_id', 'avito_item_name'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
