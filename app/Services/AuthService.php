<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use function PHPUnit\Framework\throwException;

class AuthService
{
    public function createPassword(): string
    {
        $password =  Str::random(32);

        Log::info('New password generated', [
            'password' => $password
        ]);

        return $password;
    }

    public function createToken(): string
    {
        $token = Str::random(64);

        Log::info('New token generated', [
            'token' => $token
        ]);

        return $token;
    }

    public function preparedPassword(string $password): string
    {
        return trim($password);
    }

    public function createVerificationUrl(string $token): string
    {
        try {
            $verificationUrl = URL::temporarySignedRoute('auth.verification', now()->addHour(), [
                'token' => $token
            ]);

            Log::info('New verification url generated', [
                'url' => $verificationUrl
            ]);

            return $verificationUrl;
        } catch (Exception $e) {
            Log::error('Failed to create a new verification url', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);

            throw $e;
        }
    }

    public function createUserDashboardUrl(string $token): string
    {
        try {
            $dashboardUrl = URL::temporarySignedRoute('dashboard', now()->addHour(), [
                'token' => $token
            ]);

            Log::info('New dashboard url generated', [
                'dashboard_url' => $dashboardUrl
            ]);

            return $dashboardUrl;
        } catch (Exception $e) {
            Log::error('Failed to create a new verification url', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);

            throw $e;
        }
    }

    public function createUserDetailCache(string $token, string $email): bool
    {
        try {
            $action = Cache::put("user_detail_{$token}", [
                'email' => $email,
            ], now()->addHour());

            if (!$action) {
                Log::warning('Failed to create new user detail cache', [
                    'token' => $token
                ]);

                throw new Exception('Faield to create new user detail cache');
            }

            $cacheData = Cache::get("user_detail_{$token}");

            Log::info('New User Detail Cache', [
                'cache_data' => $cacheData
            ]);

            return $action;
        } catch (Exception $e) {
            Log::error('Failed to create user detail cache in service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            throw $e;
        }
    }

    public function createCache(string $token, string $email, string $password): bool
    {
        try {
            $action =  Cache::put("password_verification_{$token}", [
                'email' => $email,
                'password' => $password,
            ], now()->addHour());

            if (!$action) {
                Log::warning('Failed to create new cache', [
                    'token' => $token
                ]);

                throw new Exception('Failed to create new cache');
            }

            $cacheData = $this->getCache($token);

            Log::info('New cache created', [
                'cache_data' => $cacheData
            ]);

            return $action;
        } catch (Exception $e) {
            Log::error('Failed to create cache in service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            throw $e;
        }
    }

    public function forgetCache(string $token): bool
    {
        try {
            $check = $this->checkCache($token);

            if (!$check) {
                return false;
            }

            $action = Cache::forget("password_verification_{$token}");

            if (!$action) {
                Log::warning('No cache found for token', [
                    'token' => $token
                ]);

                throw new Exception('No cache to forget');
            }

            Log::info('Successfully deleted cache', [
                'token' => $token
            ]);

            return $action;
        } catch (Exception $e) {
            Log::error('Failed to forget cache in service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token,
            ]);

            throw $e;
        }
    }

    public function forgetUserCache(string $token): bool
    {
        try {
            $check = $this->checkUserCache($token);

            if (!$check) {
                return false;
            }

            $action = Cache::forget("user_detail_{$token}");

            if (!$action) {
                Log::warning('No user cache found for token', [
                    'token' => $token,
                ]);

                throw new Exception('No user cache to forget');
            }

            Log::info('Successfully deleted user cache', [
                'token' => $token
            ]);

            return $action;
        } catch (Exception $e) {
            Log::error('Failed to forget cache in service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            throw $e;
        }
    }

    public function getCache(string $token): array
    {
        try {
            $data = Cache::get("password_verification_{$token}");

            if ($data === null) {
                Log::warning('No cache found for token', [
                    'token' => $token
                ]);

                throw new Exception('No cache to fetch');
            }

            return $data;
        } catch (Exception $e) {
            Log::error('Failed to get cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            throw $e;
        }
    }

    public function getUserCache(string $token): array
    {
        try {
            $data = Cache::get("user_detail_{$token}");

            if ($data === null) {
                Log::warning('No user cache detail found for token', [
                    'token' => $token,
                ]);

                throw new Exception('No user cache to fetch');
            }

            return $data;
        } catch (Exception $e) {
            Log::error('Failed to get user cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);

            throw $e;
        }
    }

    public function storeCacheData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password']
        ];
    }

    public function storeUserCacheData(array $data): array
    {
        return [
            'email' => $data['email'],
        ];
    }

    public function checkCache(string $token): bool
    {
        return Cache::has("password_verification_{$token}");
    }

    public function checkUserCache(string $token): bool
    {
        return Cache::has("user_detail_{$token}");
    }

    public function verifyPassword(string $password, array $data): bool
    {
        return $password === $data['password'];
    }
}
