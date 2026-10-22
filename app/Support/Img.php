<?php

namespace App\Support;

use Statamic\Contracts\Assets\Asset as AssetContract;
use Statamic\Facades\Asset;
use Statamic\Facades\Image;
use Statamic\Fields\Value;
use Throwable;

/**
 * URL gambar teroptimasi (Glide: resize + WebP).
 *
 * Selalu fallback ke URL asli bila bukan asset raster, gambar terlalu
 * besar untuk GD, atau terjadi error, sehingga gambar tidak pernah rusak.
 */
class Img
{
    // Batas piksel aman untuk GD (memory_limit 128M).
    protected const MAX_PIXELS = 20_000_000;

    protected const RASTER = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * URL gambar dengan lebar maksimum $width.
     */
    public static function url($value, int $width = 1600, int $quality = 75): string
    {
        $original = static::originalUrl($value);

        if (! config('app.image_optimization', true)) {
            return $original;
        }

        try {
            $asset = static::resolveAsset($value);

            if (! $asset || ! static::canOptimize($asset)) {
                return $original;
            }

            $params = ['w' => $width, 'fit' => 'max', 'q' => $quality];

            if (function_exists('imagewebp')) {
                $params['fm'] = 'webp';
            }

            // Pakai ID asset agar URL tanpa ekstensi file: nginx production
            // membuang query string (signature) untuk URL berakhiran .jpg/.png.
            return (string) Image::manipulate($asset->id(), $params);
        } catch (Throwable $e) {
            report($e);

            return $original;
        }
    }

    /**
     * Atribut srcset untuk beberapa lebar.
     */
    public static function srcset($value, array $widths = [640, 1024, 1600, 1920], int $quality = 75): string
    {
        $asset = static::resolveAsset($value);

        if (! $asset || ! static::canOptimize($asset) || ! config('app.image_optimization', true)) {
            return '';
        }

        $maxWidth = (int) $asset->width();

        return collect($widths)
            ->filter(fn ($w) => ! $maxWidth || $w <= $maxWidth)
            ->whenEmpty(fn ($c) => $c->push($maxWidth))
            ->map(fn ($w) => static::url($asset, $w, $quality).' '.$w.'w')
            ->implode(', ');
    }

    protected static function canOptimize(AssetContract $asset): bool
    {
        if (! in_array(strtolower($asset->extension()), self::RASTER, true)) {
            return false;
        }

        $pixels = (int) $asset->width() * (int) $asset->height();

        return $pixels > 0 && $pixels <= self::MAX_PIXELS;
    }

    protected static function resolveAsset($value): ?AssetContract
    {
        if ($value instanceof Value) {
            $value = $value->value();
        }

        if ($value instanceof AssetContract) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return str_contains($value, '::')
                ? Asset::find($value)
                : Asset::findByUrl($value);
        } catch (Throwable $e) {
            return null;
        }
    }

    protected static function originalUrl($value): string
    {
        if ($value instanceof Value) {
            return (string) $value;
        }

        if ($value instanceof AssetContract) {
            return (string) $value->url();
        }

        return is_string($value) ? $value : (string) ($value ?? '');
    }
}
