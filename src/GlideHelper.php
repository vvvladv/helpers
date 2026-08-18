<?php

namespace QmediaBy\Helpers;

use Illuminate\Support\Facades\Log;
use League\Glide\Server;
use League\Glide\ServerFactory;
use QmediaBy\Helpers\Support\Package;
use Throwable;

class GlideHelper
{
    /**
     * @var ?Server
     */
    private static ?Server $server = null;

    /**
     * @param string $path
     * @param array|string $params
     */
    public static function process($path, $params = [], bool $needReturnNoimage = true): string
    {
        $path = ltrim((string) $path, '/');

        if ($path === '' || !file_exists(Package::publicPath($path))) {
            if (!$needReturnNoimage) {
                return '';
            }

            $path = Package::noImage();
        }

        $paramsArray = is_string($params) ? static::parseParams($params) : $params;
        $cacheRelative = trim((string) Package::config('glide.cache', 'assets/cache/thumbs'), '/');

        try {
            $server = static::server();

            if (!$server->cacheFileExists($path, $paramsArray)) {
                $server->makeImage($path, $paramsArray);
            }

            return '/' . $cacheRelative . '/' . $server->getCachePath($path, $paramsArray);
        } catch (Throwable $e) {
            Log::error('GlideHelper::process failed: ' . $e->getMessage());

            return '/' . ltrim($path, '/');
        }
    }

    /**
     * @return void
     */
    public static function reset(): void
    {
        self::$server = null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function parseParams(string $params): array
    {
        $queryString = str_replace([' ', ','], ['', '&'], $params);
        parse_str($queryString, $paramsArray);

        return is_array($paramsArray) ? $paramsArray : [];
    }

    /**
     * @return Server
     */
    private static function server(): Server
    {
        if (self::$server instanceof Server) {
            return self::$server;
        }

        $cacheRelative = trim((string) Package::config('glide.cache', 'assets/cache/thumbs'), '/');
        $tempRelative = trim((string) Package::config('glide.temp_dir', 'assets/cache'), '/');
        $cachePath = Package::publicPath($cacheRelative);
        $tempPath = Package::publicPath($tempRelative);

        static::ensureDirectory($cachePath);
        static::ensureDirectory($tempPath);

        self::$server = ServerFactory::create([
            'source' => Package::publicPath(),
            'cache' => $cachePath,
            'temp_dir' => $tempPath,
            'driver' => static::driver(),
            'cache_with_file_extensions' => true,
            'defaults' => Package::config('glide.defaults', [
                'fm' => 'webp',
                'q' => 80,
                'sharp' => 5,
                'filt' => 'lanczos',
            ]),
        ]);

        return self::$server;
    }

    /**
     * @return string
     */
    private static function driver(): string
    {
        $driver = Package::config('glide.driver');

        if (is_string($driver) && $driver !== '') {
            return $driver;
        }

        return extension_loaded('imagick') ? 'imagick' : 'gd';
    }

    /**
     * @param string $path
     * @return void
     */
    private static function ensureDirectory(string $path): void
    {
        if ($path === '' || is_dir($path)) {
            return;
        }

        mkdir($path, 0755, true);
    }
}
