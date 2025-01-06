<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FastTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'tag',
        'company_id'
    ];

    public function fmTags(): BelongsToMany
    {
        return $this->belongsToMany(FmTag::class);
    }

    public function scopeRelatedToMe(Builder $builder): void
    {
        $builder->whereIn('company_id', session('selectedCompanyId'));
    }
}
