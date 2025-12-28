<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalculationResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'distance_km' => $this['distance_km'],
            'duration_sec' => $this['duration_sec'],
            'duration_min' => $this['duration_min'],
            'duration_hm' => $this['duration_hm'],
            'polyline' => $this['polyline'],
            'toll_currency' => $this['toll_currency'] ?? $this['currency'] ?? 'EUR',

            'fuel_liters' => $this['fuel_liters'],
            'fuel_price_per_liter' => $this['fuel_price_per_liter'],
            'fuel_cost' => $this['fuel_cost'],

            'toll_cost' => $this['toll_cost'],
            'vignette_cost' => $this['vignette_cost'],
            'has_vignette' => $this['has_vignette'],

            'currency' => $this['currency'] ?? 'EUR',
            'total_cost' => $this['total_cost'],

            'alternative' => $this['alternative'],
        ];
    }
}
