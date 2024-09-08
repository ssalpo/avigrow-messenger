<?php

namespace App\Models;

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
        'token_refreshed_at'
    ];

    protected $hidden = [
        'external_id',
        'external_client_id',
        'external_client_secret',
        'external_access_token',
        'external_refresh_token',
        'external_access_token_expire_in',
        'token_refreshed_at'
    ];

    protected $casts = [
        'token_refreshed_at' => 'datetime'
    ];

    public function analyzeReviews(): HasMany
    {
        return $this->hasMany(AnalyzeReview::class);
    }
}
