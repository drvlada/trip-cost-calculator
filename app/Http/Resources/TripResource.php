<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,

            'distance_km' => (float) $this->distance_km,

            'fuel_type' => $this->fuel_type,
            'fuel_country_iso2' => $this->fuel_country_iso2,
            'consumption_l_per_100km' => (float) $this->consumption_l_per_100km,
            'has_vignette' => (bool) $this->has_vignette,

            'fuel_cost' => (float) $this->fuel_cost,
            'toll_cost' => (float) $this->toll_cost,
            'vignette_cost' => (float) $this->vignette_cost,
            'total_cost' => (float) $this->total_cost,

            'input' => $this->input,
            'result' => $this->result,

            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
