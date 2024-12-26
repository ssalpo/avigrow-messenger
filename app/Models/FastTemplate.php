<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FastTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'tag'
    ];

    public function fmTags(): BelongsToMany
    {
        return $this->belongsToMany(FmTag::class);
    }
}
