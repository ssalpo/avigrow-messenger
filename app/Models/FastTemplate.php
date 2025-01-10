<?php

namespace App\Models;

use App\Services\UserService;
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
        'company_id',
        'number_of_uses'
    ];

    public function fmTags(): BelongsToMany
    {
        return $this->belongsToMany(FmTag::class);
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
