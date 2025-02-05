<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendResetPasswordOtp;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

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

    /**
     * @throws RandomException
     */
    public function sendResetPasswordOtp(string $email): void
    {
        $number = random_int(10000, 99999);

        $user = User::whereEmail($email)->firstOrFail();

        $user->forceFill(['reset_password_otp' => $number])->save();

        $user->notify(new SendResetPasswordOtp($number));
    }

    /**
     * @throws ValidationException
     */
    public function resetPassword(array $data): void
    {
        $user = User::where([
            'email' => $data['email'],
            'reset_password_otp' => $data['reset_password_otp'],
        ])->first();

        if(!$user) {
            throw ValidationException::withMessages([
                'reset_password_otp' => 'Некорректный код подтверждения!'
            ]);
        }

        $user->forceFill([
            'password' => bcrypt($data['password']),
            'reset_password_otp' => null
        ])->save();
    }
}
