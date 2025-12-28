<?php

return [
    'api_key' => env('HERE_API_KEY'),

    // Public endpoints for HERE v8 Routing & Geocoding/Search.
    'routing_base_url' => env('HERE_ROUTING_BASE_URL', 'https://router.hereapi.com'),
    'geocoding_base_url' => env('HERE_GEOCODING_BASE_URL', 'https://geocode.search.hereapi.com'),
];
