<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PublicAssetUrl
{
    public static function from(?string $path, string $fallback = 'generic'): string
    {
        if (! $path) {
            return self::fallbackUrl($fallback);
        }

        if (str_starts_with($path, '/')) {
            return self::publicPathUrl($path) ?? self::fallbackUrl($fallback);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $normalized = self::normalizeAbsoluteStorageUrl($path);

            return $normalized ?? $path;
        }

        if (! Storage::disk('public')->exists($path)) {
            return self::fallbackUrl($fallback);
        }

        $url = Storage::disk('public')->url($path);

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return rtrim((string) config('app.url'), '/').'/'.ltrim($url, '/');
    }

    private static function normalizeAbsoluteStorageUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return null;
        }

        return self::publicPathUrl($path);
    }

    private static function publicPathUrl(string $path): ?string
    {
        $normalizedPath = '/'.ltrim($path, '/');

        if (str_starts_with($normalizedPath, '/storage/')) {
            $storagePath = substr($normalizedPath, strlen('/storage/'));

            if ($storagePath !== false && Storage::disk('public')->exists($storagePath)) {
                return rtrim((string) config('app.url'), '/').$normalizedPath;
            }

            return null;
        }

        if (File::exists(public_path(ltrim($normalizedPath, '/')))) {
            return rtrim((string) config('app.url'), '/').$normalizedPath;
        }

        return null;
    }

    private static function fallbackUrl(string $fallback): string
    {
        $file = match ($fallback) {
            'doctor' => '/assets/img/placeholders/doctor.svg',
            default => '/assets/img/placeholders/generic.svg',
        };

        return rtrim((string) config('app.url'), '/').$file;
    }
}
