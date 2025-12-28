<?php

namespace App\Services;

use App\Models\Country;
use App\Models\FuelPrice;
use App\Models\FuelType;
use App\Services\Here\HereApiException;
use App\Services\Here\HereGeocodingService;
use App\Services\Here\HereRoutingService;

class TripCalculationService
{
    public function __construct(
        private readonly HereGeocodingService $geocoding,
        private readonly HereRoutingService $routing
    ) {}

    public function formatDurationHm(int $seconds): string
    {
        $totalMinutes = (int) round($seconds / 60);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%dh %dm', $hours, $minutes);
    }

    /**
     * @param array{
     *   origin:string,
     *   destination:string,
     *   fuel_type:string,
     *   fuel_country_iso2:string,
     *   consumption:float|int|string,
     *   avoid_tolls?:mixed,
     *   has_vignette?:mixed
     * } $validated
     *
     * @return array<string,mixed> Result payload used by blade and API.
     *
     * @throws HereApiException
     */
    public function calculate(array $validated): array
    {
        $avoidTolls = !empty($validated['avoid_tolls']);
        $hasVignette = !empty($validated['has_vignette']);

        // 1) HERE: geocode
        $origin = $this->geocoding->geocodeOne($validated['origin']);
        $destination = $this->geocoding->geocodeOne($validated['destination']);

        // 2) MAIN route
        $mainRoute = $this->routing->routeCar(
            originLat: $origin['lat'],
            originLng: $origin['lng'],
            destinationLat: $destination['lat'],
            destinationLng: $destination['lng'],
            avoidTollRoads: false,
            includeTolls: true,
            currency: 'EUR',
            hasAllVignettes: $hasVignette
        );

        // 3) Optional ALTERNATIVE route (avoid toll roads)
        $altRoute = null;
        if ($avoidTolls) {
            try {
                $altRoute = $this->routing->routeCar(
                    originLat: $origin['lat'],
                    originLng: $origin['lng'],
                    destinationLat: $destination['lat'],
                    destinationLng: $destination['lng'],
                    avoidTollRoads: true,
                    includeTolls: true,
                    currency: 'EUR',
                    hasAllVignettes: $hasVignette
                );
            } catch (HereApiException) {
                $altRoute = null;
            }
        }

        // 4) Fuel price lookup
        $countryId = Country::where('iso2', $validated['fuel_country_iso2'])->value('id');
        $fuelTypeId = FuelType::where('code', $validated['fuel_type'])->value('id');

        $priceRow = FuelPrice::where('country_id', $countryId)
            ->where('fuel_type_id', $fuelTypeId)
            ->orderByDesc('created_at')
            ->first();

        $pricePerLiter = $priceRow?->price_per_liter ? (float) $priceRow->price_per_liter : 0.0;
        $currency = $priceRow?->currency ?? 'EUR';

        // 5) MAIN costs
        $consumption = (float) $validated['consumption'];

        $mainDistanceKm = (float) ($mainRoute['distance_km'] ?? 0.0);
        $mainFuelLiters = $mainDistanceKm * $consumption / 100.0;
        $mainFuelCost = $mainFuelLiters * $pricePerLiter;

        $mainTollCost = (float) ($mainRoute['toll_cost'] ?? 0.0);

        // MVP rule: checkbox means "already purchased" => extra vignette cost is 0
        $vignetteCost = 0.0;

        $mainTotal = $mainFuelCost + $mainTollCost + $vignetteCost;

        // 6) ALTERNATIVE costs
        $altBlock = null;
        if ($altRoute) {
            $altDistanceKm = (float) ($altRoute['distance_km'] ?? 0.0);
            $altFuelLiters = $altDistanceKm * $consumption / 100.0;
            $altFuelCost = $altFuelLiters * $pricePerLiter;

            $altTollCost = (float) ($altRoute['toll_cost'] ?? 0.0);
            $altTotal = $altFuelCost + $altTollCost + $vignetteCost;

            $altBlock = [
                'distance_km' => round($altDistanceKm, 2),
                'duration_sec' => (int) ($altRoute['duration_sec'] ?? 0),
                'duration_min' => (int) round(((int) ($altRoute['duration_sec'] ?? 0)) / 60),
                'duration_hm' => $this->formatDurationHm((int) ($altRoute['duration_sec'] ?? 0)),
                'polyline' => (string) ($altRoute['polyline'] ?? ''),
                'toll_cost' => round($altTollCost, 2),
                'fuel_liters' => round($altFuelLiters, 2),
                'fuel_cost' => round($altFuelCost, 2),
                'vignette_cost' => round($vignetteCost, 2),
                'total_cost' => round($altTotal, 2),
            ];
        }

        // 7) Result payload (keys used in blade)
        return [
            'distance_km' => round($mainDistanceKm, 2),
            'duration_sec' => (int) ($mainRoute['duration_sec'] ?? 0),
            'duration_min' => (int) round(((int) ($mainRoute['duration_sec'] ?? 0)) / 60),
            'duration_hm' => $this->formatDurationHm((int) ($mainRoute['duration_sec'] ?? 0)),
            'polyline' => (string) ($mainRoute['polyline'] ?? ''),
            'toll_currency' => $mainRoute['toll_currency'] ?? $currency,

            'fuel_liters' => round($mainFuelLiters, 2),
            'fuel_price_per_liter' => round($pricePerLiter, 3),
            'fuel_cost' => round($mainFuelCost, 2),

            'toll_cost' => round($mainTollCost, 2),
            'vignette_cost' => round($vignetteCost, 2),
            'has_vignette' => $hasVignette,

            'currency' => $currency,
            'total_cost' => round($mainTotal, 2),

            'alternative' => $altBlock,
        ];
    }
}
