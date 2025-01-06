<?php

namespace App\Models;

use App\Enums\AccountConnectStatus;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'external_id',
        'external_client_id',
        'external_client_secret',
        'external_access_token',
        'external_refresh_token',
        'external_access_token_expire_in',
        'external_access_token_expire_date',
        'token_refreshed_at',
        'avito_name',
        'avito_profile_url',
        'webhook_handle_token',
        'connection_status',
        'bot_id',
        'company_id',
    ];

    protected $hidden = [
        'external_id',
        'external_client_id',
        'external_client_secret',
        'external_access_token',
        'external_refresh_token',
        'external_access_token_expire_in',
        'external_access_token_expire_date',
        'token_refreshed_at',
        'webhook_handle_token',
        'connection_errors',
        'connection_status',
        'bot_id',
    ];

    protected $casts = [
        'token_refreshed_at' => 'datetime',
        'external_access_token_expire_date' => 'datetime',
        'connection_status' => AccountConnectStatus::class
    ];

    public function analyzeReviews(): HasMany
    {
        return $this->hasMany(AnalyzeReview::class);
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeRelatedToMe(Builder $builder): void
    {
        $builder->whereIn('company_id', UserService::relatedCompanyIds(\auth()?->user()));
    }

    public function scopeCurrentCompany(Builder $builder): void
    {
        $builder->where('company_id', session('selectedCompanyId'));
    }

    public function scopeIsOwner(Builder $builder): void
    {
        $builder->whereHas('company', function ($query) {
            $query->where('created_by', \auth()?->id());
        });
    }
}
