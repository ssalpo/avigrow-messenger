<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'receipt_by',
        'receipt_at',
        'product_type',
    ];

    protected $casts = [
        'receipt_at' => 'datetime'
    ];
}
