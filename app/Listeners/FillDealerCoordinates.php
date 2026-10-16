<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Http;
use Statamic\Events\EntrySaving;
use Throwable;

class FillDealerCoordinates
{
    /**
     * Isi Map Coordinates dealer dari Google Maps URL.
     */
    public function handle(EntrySaving $event): void
    {
        $entry = $event->entry;

        if ($entry->collectionHandle() !== 'dealers') {
            return;
        }

        $url = trim((string) $entry->get('google_maps_url'));
        $location = $entry->get('location') ?? [];
        $hasCoords = filled($location['latitude'] ?? null) && filled($location['longitude'] ?? null);

        if (! $url || ($hasCoords && ! $entry->isDirty('google_maps_url'))) {
            return;
        }

        if (! $coords = self::coordinates($url)) {
            return;
        }

        $entry->set('location', array_merge($location, [
            'latitude' => $coords[0],
            'longitude' => $coords[1],
        ]));
    }

    /**
     * lat, lng
     */
    public static function coordinates(string $url): ?array
    {
        if ($coords = self::parse($url)) {
            return $coords;
        }

        // Short link
        if (! preg_match('#^https?://(maps\.app\.goo\.gl|goo\.gl|g\.co)/#i', $url)) {
            return null;
        }

        try {
            $response = Http::timeout(10)->withoutRedirecting()->get($url);
            $location = $response->header('Location');

            return $location ? self::coordinates($location) : null;
        } catch (Throwable) {
            return null;
        }
    }

    protected static function parse(string $url): ?array
    {
        $url = urldecode($url);

        $patterns = [
            '/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/',
            '/@(-?\d+\.\d+),(-?\d+\.\d+)/',
            '/[?&](?:q|query|ll|center|destination)=(-?\d+\.\d+),\s*(-?\d+\.\d+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return [(float) $m[1], (float) $m[2]];
            }
        }

        return null;
    }
}
