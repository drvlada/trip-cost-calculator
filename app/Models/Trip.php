<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'from',
        'to',
        'distance_km',
        'fuel_type',
        'consumption_l_per_100km',
        'fuel_country_iso2',
        'has_vignette',
        'fuel_cost',
        'toll_cost',
        'vignette_cost',
        'total_cost',
    ];


    protected $casts = [
        'distance_km' => 'float',
        'total_cost' => 'float',
        'has_vignette' => 'boolean',
        'input' => 'array',
        'result' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
