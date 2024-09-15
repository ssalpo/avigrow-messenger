<?php

namespace App\Models;

use App\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'product_id',
        'chat_id',
        'base_price',
        'price',
        'comment',
        'status'
    ];

    protected $casts = [
        'base_price' => 'double',
        'price' => 'double'
    ];

    protected $appends = [
        'created_at_formatted',
        'is_cancel'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->created_at->format('Y-m-d H:i'),
        );
    }

    public function isCancel(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->status === OrderStatus::CANCEL->value,
        );
    }
}
