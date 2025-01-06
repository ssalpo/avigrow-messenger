<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FmTag extends Model
{
    protected $fillable = [
        'name',
        'company_id'
    ];

    public function fastTemplates(): BelongsToMany
    {
        return $this->belongsToMany(FastTemplate::class);
    }

    public function scopeRelatedToMe(Builder $builder): void
    {
        $builder->where('company_id', session('selectedCompanyId'));
    }
}
