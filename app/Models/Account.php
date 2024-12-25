<?php

namespace App\Models;

use App\Enums\AccountConnectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'connection_errors'
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
}
