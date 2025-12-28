<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\FuelPrice;
use App\Models\FuelType;
use App\Models\Trip;
use App\Services\Here\HereApiException;
use App\Services\Here\HereGeocodingService;
use App\Services\Here\HereRoutingService;
use App\Services\TripCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly TripCalculationService $calculator,
        private readonly HereGeocodingService $geocoding,
        private readonly HereRoutingService $routing
    ) {}

    public function home(): View
    {
        return view('pages.home');
    }

    public function calculator(): View
    {
        return view('pages.calculator', [
            'fuelTypes' => FuelType::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'input' => session('calculator_input', []),
        ]);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'fuel_type' => ['required', 'string', 'max:32'],
            'fuel_country_iso2' => ['required', 'string', 'size:2'],
            'consumption' => ['required', 'numeric', 'min:0.1'],
            'avoid_tolls' => ['nullable'],
            'has_vignette' => ['nullable'], 
        ]);

        try {
            $result = $this->calculator->calculate($validated);
        } catch (HereApiException $e) {
            return back()->withInput()->withErrors(['route' => $e->getMessage()]);
        }

        session([
            'calculator_result' => $result,
            'calculator_input' => $request->all(),
        ]);

        // Save to history only for authenticated users.
        if (Auth::check()) {
            Trip::create([
                'user_id' => Auth::id(),

                // route
                'from' => $validated['origin'],
                'to' => $validated['destination'],
                'distance_km' => (int) round($result['distance_km'] ?? 0),

                // options / inputs
                'fuel_type' => $validated['fuel_type'],
                'consumption_l_per_100km' => (float) $validated['consumption'],
                'fuel_country_iso2' => $validated['fuel_country_iso2'],

                'has_vignette' => (bool) ($validated['has_vignette'] ?? false),

                // costs
                'fuel_cost' => (float) ($result['fuel_cost'] ?? 0),
                'toll_cost' => (float) ($result['toll_cost'] ?? 0),
                'vignette_cost' => (float) ($result['vignette_cost'] ?? 0),
                'total_cost' => (float) ($result['total_cost'] ?? 0),
            ]);
        }

        return redirect()->route('calculator.result');
    }

    public function calculatorResult()
    {
        $result = session('calculator_result');
        $input = session('calculator_input');

        if (!$result || !$input) {
            return redirect()->route('calculator');
        }

        return view('pages.calculator-result', [
            'result' => $result,
            'input' => $input,
            'hereApiKey' => config('here.api_key'),
        ]);
    }

    public function history(): View
    {
        $trips = collect();

        if (Auth::check()) {
            $trips = Trip::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view('pages.history', [
            'trips' => $trips,
        ]);
    }


    public function calculatorFromHistory(Trip $trip)
    {

        if (!Auth::check() || $trip->user_id !== Auth::id()) {
            abort(403);
        }

        $input = [
            'origin' => $trip->from,
            'destination' => $trip->to,
            'fuel_type' => $trip->fuel_type,

            'fuel_country_iso2' => $trip->fuel_country_iso2 ?? 'DE',
            'consumption' => $trip->consumption_l_per_100km ?? 7.5,

            'has_vignette' => $trip->has_vignette ? '1' : null,
            'avoid_tolls' => null,
        ];

        $distanceKm = (float) $trip->distance_km;
        $durationSec = 0;
        $polyline = '';

        try {
            $origin = $this->geocoding->geocodeOne($trip->from);
            $destination = $this->geocoding->geocodeOne($trip->to);

            $route = $this->routing->routeCar(
                originLat: $origin['lat'],
                originLng: $origin['lng'],
                destinationLat: $destination['lat'],
                destinationLng: $destination['lng'],
                avoidTollRoads: false,
                includeTolls: true,
                currency: 'EUR',
                hasAllVignettes: (bool) $trip->has_vignette
            );

            $distanceKm = (float) ($route['distance_km'] ?? $distanceKm);
            $durationSec = (int) ($route['duration_sec'] ?? 0);
            $polyline = (string) ($route['polyline'] ?? '');
        } catch (\Throwable) {
        }

        $countryId = Country::where('iso2', $input['fuel_country_iso2'])->value('id');
        $fuelTypeId = FuelType::where('code', $trip->fuel_type)->value('id');

        $priceRow = FuelPrice::where('country_id', $countryId)
            ->where('fuel_type_id', $fuelTypeId)
            ->orderByDesc('created_at')
            ->first();

        $pricePerLiter = $priceRow?->price_per_liter ? (float) $priceRow->price_per_liter : 0.0;
        $currency = $priceRow?->currency ?? 'EUR';

        $fuelLiters = null;
        if ($pricePerLiter > 0) {
            $fuelLiters = round(((float) $trip->fuel_cost) / $pricePerLiter, 2);
        }

        $result = [
            'distance_km' => round($distanceKm, 2),
            'duration_sec' => $durationSec,
            'duration_min' => (int) round($durationSec / 60),
            'duration_hm' => $durationSec > 0 ? $this->calculator->formatDurationHm($durationSec) : '—',
            'polyline' => $polyline,
            'toll_currency' => $currency,

            'fuel_liters' => $fuelLiters,
            'fuel_price_per_liter' => $pricePerLiter > 0 ? round($pricePerLiter, 3) : null,
            'fuel_cost' => (float) $trip->fuel_cost,

            'toll_cost' => (float) $trip->toll_cost,
            'vignette_cost' => (float) $trip->vignette_cost,
            'has_vignette' => (bool) $trip->has_vignette,

            'currency' => $currency,
            'total_cost' => (float) $trip->total_cost,

            'alternative' => null,
        ];

        session([
            'calculator_input' => $input,
            'calculator_result' => $result,
        ]);

        return redirect()->route('calculator.result');
    }

    public function historyDestroy(Trip $trip)
    {
        if (!Auth::check() || $trip->user_id !== Auth::id()) {
            abort(403);
        }

        $trip->delete();

        return redirect()->route('history')->with('status', 'Trip deleted.');
    }
}
