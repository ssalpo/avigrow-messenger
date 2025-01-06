<?php

namespace App\Models;

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
}
