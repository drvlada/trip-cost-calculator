<?php

use App\Http\Controllers\Api\TripController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Lightweight REST API layer over existing services.
| Authentication is session-based (same as web).
|
*/

// Public: calculation endpoint (no auth)
Route::post('/calculate', [TripController::class, 'calculate']);

/*
| Authenticated endpoints
| We explicitly include "web" middleware so that:
| - session cookies are available
| - auth() works the same way as in web routes
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/trips', [TripController::class, 'index']);
    Route::get('/trips/{trip}', [TripController::class, 'show']);
});
