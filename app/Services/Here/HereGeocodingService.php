<?php

namespace App\Services\Here;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HereGeocodingService
{
    /**
     * Geocode a user-provided string into a single coordinate pair.
     *
     * @return array{lat: float, lng: float, label: string}
     */
    public function geocodeOne(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            throw new HereApiException('Origin/Destination must not be empty.');
        }

        $cacheKey = 'here:geocode:' . md5(mb_strtolower($query));

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($query) {
            $baseUrl = config('here.geocoding_base_url');
            $apiKey = config('here.api_key');

            if (!$apiKey) {
                throw new HereApiException('HERE_API_KEY is not configured.');
            }

            $response = Http::timeout(10)
                ->retry(2, 200)
                ->get(rtrim($baseUrl, '/') . '/v1/geocode', [
                    'q' => $query,
                    'apiKey' => $apiKey,
                ]);

            if (!$response->ok()) {
                throw new HereApiException('HERE Geocoding API request failed.');
            }

            $data = $response->json();
            $item = $data['items'][0] ?? null;

            if (!$item || empty($item['position'])) {
                throw new HereApiException('Location not found: ' . $query);
            }

            return [
                'lat' => (float) $item['position']['lat'],
                'lng' => (float) $item['position']['lng'],
                'label' => (string) ($item['address']['label'] ?? $query),
            ];
        });
    }
}
