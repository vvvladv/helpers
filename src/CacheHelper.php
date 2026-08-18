<?php

namespace QmediaBy\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use QmediaBy\Helpers\Support\Package;

class CacheHelper
{
    /**
     * @param ...$params
     * @return string
     */
    public static function createKey(...$params): string
    {
        if (static::enable() === false) {
            return '';
        }

        $normalized = [];

        foreach ($params as $param) {
            if (is_array($param) || is_object($param)) {
                $normalized[] = json_encode($param);
                continue;
            }

            $normalized[] = (string) $param;
        }

        $normalized[] = static::clientKey();

        return md5(implode(',', $normalized));
    }

    /**
     * @param string $cacheKey
     * @return bool
     */
    public static function has(string $cacheKey): bool
    {
        return static::enable() && Cache::has($cacheKey);
    }

    /**
     * @param string $cacheKey
     * @return mixed
     */
    public static function get(string $cacheKey): mixed
    {
        if (!static::enable()) {
            return null;
        }

        return Cache::get($cacheKey);
    }

    /**
     * @param string $cacheKey
     * @param callable $callback
     * @param array $args
     * @return mixed
     */
    public static function getWithCallback(string $cacheKey, callable $callback, array $args = []): mixed
    {
        if (!static::enable()) {
            return call_user_func($callback, $args);
        }

        return Cache::rememberForever($cacheKey, static fn () => call_user_func($callback, $args));
    }

    /**
     * @param string $cacheKey
     * @param mixed $value
     * @param int $seconds
     * @return void
     */
    public static function set(string $cacheKey, mixed $value, int $seconds = 0): void
    {
        if (!static::enable()) {
            return;
        }

        if ($seconds <= 0) {
            Cache::forever($cacheKey, $value);
            return;
        }

        Cache::put($cacheKey, $value, $seconds);
    }

    /**
     * @return bool
     */
    public static function enable(): bool
    {
        if (!function_exists('evo')) {
            return false;
        }

        return (int) (evo()->config['enable_cache'] ?? 0) === 1;
    }

    /**
     * @param int $hour
     * @return int
     */
    public static function secondsToHour(int $hour): int
    {
        $startTime = Carbon::now();
        $finishTime = $startTime->copy()->endOfDay()->addHours($hour);

        return $finishTime->diffInSeconds($startTime);
    }

    /**
     * @return string
     */
    private static function clientKey(): string
    {
        $configured = Package::config('cache.client_key');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        if (!function_exists('evo')) {
            return '';
        }

        return (string) (
            evo()->config['client_cache_key']
            ?? evo()->config['client_dev_cache_key']
            ?? ''
        );
    }
}
