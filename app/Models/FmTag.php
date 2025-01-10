<?php

namespace App\Models;

use App\Services\UserService;
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
        $builder->whereIn('company_id', UserService::relatedCompanyIds(\auth()?->user()));
    }

    public function scopeCurrentCompany(Builder $builder, ?int $companyId = null): void
    {
        $builder->where('company_id', $companyId ?? request()->attributes->get('currentCompanyId'));
    }
}
