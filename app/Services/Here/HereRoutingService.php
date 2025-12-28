<?php

namespace App\Services\Here;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HereRoutingService
{
    /**
     * Calculate a car route between coordinates.
     *
     * @return array{
     *   distance_km: float,
     *   duration_sec: int,
     *   polyline: string,
     *   toll_cost: float,
     *   toll_currency: string
     * }
     */
    public function routeCar(
        float $originLat,
        float $originLng,
        float $destinationLat,
        float $destinationLng,
        bool $avoidTollRoads = false,
        bool $includeTolls = true,
        ?string $currency = null,
        bool $hasAllVignettes = false
    ): array {
        $cacheKey = 'here:route:v4:' . md5(json_encode([
            'o' => [$originLat, $originLng],
            'd' => [$destinationLat, $destinationLng],
            'mode' => 'car',
            'avoidTollRoads' => $avoidTollRoads,
            'includeTolls' => $includeTolls,
            'currency' => $currency,
            'hasAllVignettes' => $hasAllVignettes,
        ]));

        return Cache::remember($cacheKey, now()->addHours(6), function () use (
            $originLat,
            $originLng,
            $destinationLat,
            $destinationLng,
            $avoidTollRoads,
            $includeTolls,
            $currency,
            $hasAllVignettes
        ) {
            $baseUrl = config('here.routing_base_url');
            $apiKey = config('here.api_key');

            if (!$apiKey) {
                throw new HereApiException('HERE_API_KEY is not configured.');
            }

            $returnParts = ['summary', 'polyline'];

            if ($includeTolls) {
                $returnParts[] = 'tolls';
            }

            $params = [
                'transportMode' => 'car',
                'origin' => $originLat . ',' . $originLng,
                'destination' => $destinationLat . ',' . $destinationLng,
                'return' => implode(',', $returnParts),
                'apiKey' => $apiKey,
            ];

            if ($avoidTollRoads) {
                $params['avoid[features]'] = 'tollRoad';
            }

            if ($currency) {
                $params['currency'] = $currency;
            }

            if ($includeTolls && $hasAllVignettes) {
                $params['tolls[vignettes]'] = 'all';
            }

            $response = Http::timeout(15)
                ->retry(2, 300)
                ->get(rtrim($baseUrl, '/') . '/v8/routes', $params);

            if (!$response->ok()) {
                throw new HereApiException('HERE Routing API request failed.');
            }

            $data = $response->json();
            $route = $data['routes'][0] ?? null;
            $section = $route['sections'][0] ?? null;

            if (!$section || empty($section['summary'])) {
                throw new HereApiException('Route not found.');
            }

            $summary = $section['summary'];
            $distanceMeters = (float) ($summary['length'] ?? 0);
            $durationSec = (int) ($summary['duration'] ?? 0);
            $polyline = (string) ($section['polyline'] ?? '');

            if ($distanceMeters <= 0 || $durationSec <= 0 || $polyline === '') {
                throw new HereApiException('HERE returned incomplete route data.');
            }

            $tollCost = 0.0;
            $tollCurrency = $currency ?: 'EUR';

            if ($includeTolls && !empty($section['tolls']) && is_array($section['tolls'])) {
                foreach ($section['tolls'] as $toll) {
                    $prices = [];

                    $fares = $toll['fares'] ?? null;
                    if (is_array($fares)) {
                        foreach ($fares as $fare) {
                            $converted = $fare['convertedPrice'] ?? null;
                            if (is_array($converted) && isset($converted['value'])) {
                                $prices[] = [
                                    'value' => (float) $converted['value'],
                                    'currency' => $converted['currency'] ?? $tollCurrency,
                                ];
                                continue;
                            }

                            $price = $fare['price'] ?? null;
                            if (is_array($price) && isset($price['value'])) {
                                $prices[] = [
                                    'value' => (float) $price['value'],
                                    'currency' => $price['currency'] ?? null,
                                ];
                            }
                        }
                    }

                    if (empty($prices)) {
                        continue;
                    }

                    usort($prices, fn ($a, $b) => $a['value'] <=> $b['value']);
                    $chosen = $prices[0];

                    $tollCost += $chosen['value'];

                    if (!empty($chosen['currency'])) {
                        $tollCurrency = $chosen['currency'];
                    }
                }
            }

            return [
                'distance_km' => round($distanceMeters / 1000, 2),
                'duration_sec' => $durationSec,
                'polyline' => $polyline,
                'toll_cost' => round($tollCost, 2),
                'toll_currency' => $tollCurrency,
            ];
        });
    }
}
