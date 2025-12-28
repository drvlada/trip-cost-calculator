<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalculationResultResource;
use App\Http\Resources\TripResource;
use App\Http\Resources\TripSummaryResource;
use App\Models\Trip;
use App\Services\Here\HereApiException;
use App\Services\TripCalculationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TripController extends Controller
{
    public function __construct(
        private readonly TripCalculationService $calculator
    ) {}

    /**
     * POST /api/calculate
     */
    public function calculate(Request $request): CalculationResultResource
    {
        $validated = $request->validate([
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'fuel_type' => ['required', 'string', 'max:32'],
            'fuel_country_iso2' => ['required', 'string', 'size:2'],
            'consumption' => ['required', 'numeric', 'min:0.1'],
            'avoid_tolls' => ['nullable'],
            'has_vignette' => ['nullable'], // checkbox -> "1" when checked
        ]);

        try {
            $result = $this->calculator->calculate($validated);
        } catch (HereApiException $e) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }

        return new CalculationResultResource($result);
    }

    /**
     * GET /api/trips (auth)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $trips = Trip::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(20);

        return TripSummaryResource::collection($trips);
    }

    /**
     * GET /api/trips/{id} (auth)
     */
    public function show(Trip $trip): TripResource
    {
        if ($trip->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden.');
        }

        return new TripResource($trip);
    }
}
