<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use function Laravel\Prompts\form;

class UserService
{
    public static function refreshRelatedCompaniesCache(User $user): void
    {
        $companies = $user->companies->map->only(['id', 'name'])->toArray();
        $cacheKey = self::relatedCompaniesCacheKey($user->id);

        Cache::forget($cacheKey);

        Cache::forever($cacheKey, $companies);
    }

    public static function relatedCompaniesCacheKey(int $userId): string
    {
        return sprintf('related:%s:companies', $userId);
    }

    public static function relatedCompanies(User $user): array
    {
        $key = self::relatedCompaniesCacheKey($user->id);

        if (!Cache::has($key)) {
            $companies = $user->companies->map->only(['id', 'name'])->toArray();

            Cache::put($key, $companies);

            return $companies;
        }

        return Cache::get($key);
    }

    public static function relatedCompanyIds(User $user): array
    {
        return Arr::pluck(self::relatedCompanies($user), 'id');
    }
}
