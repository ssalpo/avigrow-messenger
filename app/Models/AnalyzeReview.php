<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyzeReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'chat_id',
        'context_id',
        'context',
        'chat_sender_name'
    ];
}
