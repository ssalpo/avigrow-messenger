<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use function Laravel\Prompts\form;

class UserService
{
    public function refreshRelatedCompaniesCache(User $user): void
    {
        $companyIds = $user->companies->pluck('id');
        $cacheKey = self::relatedCompaniesCacheKey($user->id);

        Cache::forget($cacheKey);

        Cache::forever($cacheKey, $companyIds);
    }

    public static function relatedCompaniesCacheKey(int $userId): string
    {
        return sprintf('related:%s:companies', $userId);
    }

    public static function relatedCompanyIds(User $user)
    {
        $key = self::relatedCompaniesCacheKey($user->id);

        if (!Cache::has($key)) {
            $ids = $user->companies->pluck('id');

            Cache::put($key, $ids);

            return $ids;
        }

        return Cache::get($key);
    }
}
