<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'comment'
    ];

    protected $casts = [
        'receipt_at' => 'datetime'
    ];

    protected $appends = [
        'created_at_formatted'
    ];

    public function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->created_at->format('Y-m-d H:i'),
        );
    }
}
