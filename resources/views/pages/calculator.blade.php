@extends('layouts.guest')

@section('content')
    @php
        // Priority: old() (validation) > request() (history->view redirect) > session input
        $originVal = old('origin', request('origin', $input['origin'] ?? ''));
        $destinationVal = old('destination', request('destination', $input['destination'] ?? ''));

        $fuelTypeVal = old('fuel_type', request('fuel_type', $input['fuel_type'] ?? 'diesel'));
        $consumptionVal = old('consumption', request('consumption', $input['consumption'] ?? ''));
        $fuelCountryVal = old('fuel_country_iso2', request('fuel_country_iso2', $input['fuel_country_iso2'] ?? 'DE'));

        // Checkboxes: old() can be "1" or null; request() can be "1" or null
        $avoidTollsChecked = old('avoid_tolls', request('avoid_tolls', $input['avoid_tolls'] ?? null)) ? true : false;
        $hasVignetteChecked = old('has_vignette', request('has_vignette', $input['has_vignette'] ?? null)) ? true : false;
    @endphp

    <div class="text-center">
        <h1 class="text-5xl font-extrabold tracking-tight text-gray-900">
            Plan Your Trip Cost
        </h1>

        <p class="mt-3 text-base text-gray-600">
            Estimate fuel, tolls, and vignette costs for your route instantly.
        </p>
    </div>

    @if ($errors->any())
        <div class="mx-auto mt-8 max-w-3xl rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="font-semibold">Please fix the following:</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mx-auto mt-10 w-full max-w-3xl">
        <form method="POST" action="{{ route('calculator.calculate') }}"
              class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            @csrf

            <div class="flex items-start justify-between gap-4 px-6 py-5">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white">
                        <img
                            src="{{ asset('icons/car.png') }}"
                            alt="Trip details"
                            class="h-11 w-10 object-contain"
                        >
                    </div>

                    <div>
                        <div class="text-base font-semibold text-gray-900">Trip Details</div>
                        <div class="mt-1 text-sm text-gray-600">Enter your trip parameters below.</div>
                    </div>
                </div>

                <button
                type="button"
                class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-800 hover:bg-gray-50"
                onclick="
                    document.querySelector('[name=origin]').value = '';
                    document.querySelector('[name=destination]').value = '';
                    document.querySelector('[name=consumption]').value = '';

                    document.querySelector('[name=fuel_type]').value = 'diesel';
                    document.querySelector('[name=fuel_country_iso2]').value = 'DE';

                    document.querySelector('[name=avoid_tolls]').checked = false;
                    document.querySelector('[name=has_vignette]').checked = false;
                "
            >
                Clear form
            </button>
            </div>

            <div class="px-6 pb-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-base font-semibold text-gray-900">Origin</label>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-1">
                                <img
                                    src="{{ asset('icons/pin.png') }}"
                                    alt="Origin"
                                    class="h-8 w-8 object-contain opacity-70"
                                >
                            </span>

                            <input type="text" name="origin" value="{{ $originVal }}"
                                   class="w-full rounded-lg border border-gray-200 bg-gray-100 py-2 pl-10 pr-3 text-sm text-gray-900
                                          outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                                   placeholder="e.g. Berlin" required>
                        </div>
                    </div>

                    <div>
                        <label class="text-base font-semibold text-gray-900">Destination</label>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-1">
                                <img
                                    src="{{ asset('icons/pin.png') }}"
                                    alt="Destination"
                                    class="h-8 w-8 object-contain opacity-70"
                                >
                            </span>

                            <input type="text" name="destination" value="{{ $destinationVal }}"
                                   class="w-full rounded-lg border border-gray-200 bg-gray-100 py-2 pl-10 pr-3 text-sm text-gray-900
                                          outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                                   placeholder="e.g. Brussels" required>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-6">
                    <div class="text-sm font-semibold uppercase tracking-wide text-gray-500">Vehicle & Fuel</div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="text-base font-semibold text-gray-900">Fuel Type</label>
                            <select name="fuel_type"
                                    class="mt-2 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-900
                                           outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                                @foreach ($fuelTypes as $fuelType)
                                    <option value="{{ $fuelType->code }}" {{ $fuelTypeVal === $fuelType->code ? 'selected' : '' }}>
                                        {{ $fuelType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-base font-semibold text-gray-900">Consumption (L/100km)</label>
                            <input type="number" step="0.1" name="consumption" value="{{ $consumptionVal }}"
                                   class="mt-2 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-900
                                          outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                                   placeholder="7.5" required>
                        </div>

                        <div>
                            <label class="text-base font-semibold text-gray-900">Fuel Price Country</label>
                            <select name="fuel_country_iso2"
                                    class="mt-2 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-900
                                           outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->iso2 }}" {{ $fuelCountryVal === $country->iso2 ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-6">
                    <div class="text-sm font-semibold uppercase tracking-wide text-gray-500">Route Options</div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-base font-semibold text-gray-900">Avoid Tolls</div>
                                    <div class="mt-1 text-sm text-gray-600">Prefer toll-free roads where possible.</div>
                                </div>

                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" name="avoid_tolls" value="1" class="peer sr-only" {{ $avoidTollsChecked ? 'checked' : '' }}>
                                    <span class="h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-gray-900 transition"></span>
                                    <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-base font-semibold text-gray-900">Vignette</div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        Do you already have a vignette that covers this route?
                                    </div>
                                </div>

                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" name="has_vignette" value="1" class="peer sr-only" {{ $hasVignetteChecked ? 'checked' : '' }}>
                                    <span class="h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-gray-900 transition"></span>
                                    <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                </label>
                            </div>

                            <p class="mt-3 text-xs text-gray-500">
                                If checked, we assume the vignette is already purchased, so it adds <span class="font-semibold">no extra cost</span>.
                                Route tolls may still apply for roads that are not covered by vignettes.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 text-xs text-gray-600">
                        <div class="flex items-start gap-2">
                            <span class="mt-0.5">ⓘ</span>
                            <p>
                                <span class="font-semibold text-gray-900">Note:</span>
                                Estimates are based on average fuel prices and known toll data. Real-world conditions may vary.
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-4 py-3
                               text-sm font-semibold text-white shadow-sm hover:bg-black
                               focus:outline-none focus:ring-2 focus:ring-gray-900/20 focus:ring-offset-2">
                    Calculate Trip Cost <span class="ml-2">→</span>
                </button>
            </div>
        </form>
    </div>
@endsection
