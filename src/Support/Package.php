<?php

namespace QmediaBy\Helpers\Support;

final class Package
{
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function config(string $key, mixed $default = null): mixed
    {
        if (function_exists('config')) {
            return config('helpers.' . $key, $default);
        }

        return $default;
    }

    /**
     * @return string
     */
    public static function noImage(): string
    {
        return (string) static::config('noimage', 'theme/images/noimage.png');
    }

    /**
     * @param string $path
     * @return string
     */
    public static function publicPath(string $path = ''): string
    {
        $path = ltrim($path, '/');

        if (function_exists('public_path')) {
            return public_path($path);
        }

        return $path;
    }
}
