@extends('layouts.guest')

@section('content')
    <div class="mx-auto w-full max-w-6xl">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Trip Results</h1>
                <p class="mt-1 text-sm text-gray-600">Cost breakdown and route preview.</p>
            </div>

            <a href="{{ route('calculator') }}"
               class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                ← Back to calculator
            </a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            {{-- Cost card --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <div class="text-sm font-semibold text-gray-900">Estimated cost</div>
                    <div class="mt-1 text-xs text-gray-600">Main route and optional alternative.</div>

                    {{-- Tabs (ALWAYS visible) --}}
                    @php
                        $hasAlt = !empty($result['alternative']);
                        // If user requested avoid tolls -> alternative exists, open alt first; otherwise open main
                        $initialTab = $hasAlt ? 'alt' : 'main';
                    @endphp

                    <div class="mt-4">
                        <div class="rounded-full bg-gray-100 p-1" data-initial-tab="{{ $initialTab }}">
                            <div class="grid grid-cols-2 gap-1">
                                <button
                                    type="button"
                                    class="trip-tab-btn rounded-full px-4 py-2 text-sm font-semibold transition"
                                    data-tab="main"
                                    aria-selected="false"
                                >
                                    Main Route
                                </button>

                                <button
                                    type="button"
                                    class="trip-tab-btn rounded-full px-4 py-2 text-sm font-semibold transition"
                                    data-tab="alt"
                                    aria-selected="false"
                                    @if(!$hasAlt) disabled aria-disabled="true" @endif
                                >
                                    Avoid Tolls Route
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    {{-- TAB: MAIN --}}
                    <div
                        id="trip-tab-panel-main"
                        class="trip-tab-panel"
                        data-panel="main"
                    >
                        {{-- 4 summary cards --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            {{-- Card 1: Route --}}
                            <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="text-sm font-semibold text-gray-700">Route</div>

                                <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight truncate">
                                    {{ $input['origin'] }} - {{ $input['destination'] }}
                                </div>

                                <div class="mt-2 text-xs text-gray-600 truncate">
                                    Main route summary
                                </div>
                            </div>

                            {{-- Card 2: Distance --}}
                            <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="text-sm font-semibold text-gray-700">Distance</div>

                                <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight">
                                    {{ $result['distance_km'] }} km
                                </div>

                                <div class="mt-2 text-xs text-gray-600 truncate">
                                    Estimated travel time: {{ $result['duration_hm'] }}
                                </div>
                            </div>

                            {{-- Card 3: Fuel --}}
                            <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="text-sm font-semibold text-gray-700">Estimated fuel used</div>

                                <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight">
                                    Fuel used: {{ $result['fuel_liters'] }} L
                                </div>

                                <div class="mt-2 text-xs text-gray-600 truncate">
                                    Fuel price: {{ $result['currency'] }} {{ $result['fuel_price_per_liter'] }} / L
                                </div>
                            </div>

                            {{-- Card 4: Total --}}
                            <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="text-sm font-semibold text-gray-700">Total cost</div>

                                <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight truncate">
                                    {{ $result['currency'] }} {{ $result['total_cost'] }}
                                </div>

                                <div class="mt-2 text-xs text-gray-600 truncate">
                                    Main route total estimate
                                </div>
                            </div>
                        </div>

                        {{-- Cost Breakdown --}}
                        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="mb-6">
                                <div class="text-sm font-semibold text-gray-700">Cost Breakdown</div>
                            </div>

                            <div class="mt-5 grid gap-6 sm:grid-cols-2 sm:items-center">
                                {{-- Chart --}}
                                <div class="flex items-center justify-center">
                                    <div class="relative h-[240px] w-[240px]">
                                        <canvas id="costBreakdownChartMain" class="h-full w-full" aria-label="Cost breakdown chart" role="img"></canvas>
                                    </div>
                                </div>

                                {{-- Breakdown list --}}
                                <div class="space-y-3">
                                    {{-- Fuel --}}
                                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                                <img src="{{ asset('icons/fuel.png') }}" alt="Fuel" class="h-11 w-10 object-contain">
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900">Fuel</div>
                                            </div>
                                        </div>
                                        <div class="shrink-0 text-sm font-semibold text-gray-900">
                                            {{ $result['currency'] }} {{ $result['fuel_cost'] }}
                                        </div>
                                    </div>

                                    {{-- Tolls --}}
                                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                                                <img src="{{ asset('icons/tolls.png') }}" alt="Tolls" class="h-11 w-10 object-contain">
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900">Tolls</div>
                                            </div>
                                        </div>
                                        <div class="shrink-0 text-sm font-semibold text-gray-900">
                                            {{ $result['toll_currency'] ?? $result['currency'] }} {{ $result['toll_cost'] }}
                                        </div>
                                    </div>

                                    {{-- Vignette --}}
                                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                                                <img src="{{ asset('icons/vignette.png') }}" alt="Vignette" class="h-11 w-10 object-contain">
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900">Vignette</div>
                                            </div>
                                        </div>
                                        @php
                                            $hasVignetteUi = !empty($input['has_vignette']) || !empty($result['has_vignette']);
                                        @endphp

                                        <div class="shrink-0 text-sm font-semibold text-gray-900">
                                            {{ $hasVignetteUi ? 'Included' : 'Excluded' }}
                                        </div>

                                    </div>

                                    {{-- Total --}}
                                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                        <div class="flex min-w-0 items-center gap-4">
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gray-800">
                                                <img src="{{ asset('icons/total.png') }}" alt="Total" class="h-11 w-10 object-contain">
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900">Total</div>
                                            </div>
                                        </div>
                                        <div class="shrink-0 text-sm font-semibold text-gray-900">
                                            {{ $result['currency'] }} {{ $result['total_cost'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script type="application/json" id="cost-breakdown-data-main">
                            {!! json_encode([
                                'fuel' => (float) ($result['fuel_cost'] ?? 0),
                                'tolls' => (float) ($result['toll_cost'] ?? 0),
                                'vignette' => (float) ($result['vignette_cost'] ?? 0),
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
                            </script>
                        </div>
                    </div>

                    {{-- TAB: ALTERNATIVE (same layout as MAIN) --}}
                    <div
                        id="trip-tab-panel-alt"
                        class="trip-tab-panel hidden"
                        data-panel="alt"
                    >
                        @if (!$hasAlt)
                            {{-- If avoid-tolls was not requested, keep UI but show info --}}
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6">
                                <div class="text-sm font-semibold text-gray-900">Avoid Tolls Route is not available</div>
                                <p class="mt-2 text-sm text-gray-600">
                                    To calculate an alternative route, enable <span class="font-semibold">Avoid toll roads</span> in the calculator form and submit again.
                                </p>
                            </div>
                        @else
                            {{-- 4 summary cards (ALT) --}}
                            <div class="grid gap-4 sm:grid-cols-2">
                                {{-- Card 1: Route --}}
                                <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <div class="text-sm font-semibold text-gray-700">Route</div>

                                    <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight truncate">
                                        {{ $input['origin'] }} - {{ $input['destination'] }}
                                    </div>

                                    <div class="mt-2 text-xs text-gray-600 truncate">
                                        Avoid toll roads route summary
                                    </div>
                                </div>

                                {{-- Card 2: Distance --}}
                                <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <div class="text-sm font-semibold text-gray-700">Distance</div>

                                    <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight">
                                        {{ $result['alternative']['distance_km'] }} km
                                    </div>

                                    <div class="mt-2 text-xs text-gray-600 truncate">
                                        Estimated travel time: {{ $result['alternative']['duration_hm'] }}
                                    </div>
                                </div>

                                {{-- Card 3: Fuel --}}
                                <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <div class="text-sm font-semibold text-gray-700">Estimated fuel used</div>

                                    <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight">
                                        Fuel used: {{ $result['alternative']['fuel_liters'] }} L
                                    </div>

                                    <div class="mt-2 text-xs text-gray-600 truncate">
                                        Fuel price: {{ $result['currency'] }} {{ $result['fuel_price_per_liter'] }} / L
                                    </div>
                                </div>

                                {{-- Card 4: Total --}}
                                <div class="h-[120px] overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <div class="text-sm font-semibold text-gray-700">Total cost</div>

                                    <div class="mt-2 text-lg font-extrabold text-gray-900 leading-tight truncate">
                                        {{ $result['currency'] }} {{ $result['alternative']['total_cost'] }}
                                    </div>

                                    <div class="mt-2 text-xs text-gray-600 truncate">
                                        Avoid toll roads total estimate
                                    </div>
                                </div>
                            </div>

                            {{-- Cost Breakdown (ALT) --}}
                            <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                <div class="mb-6">
                                    <div class="text-sm font-semibold text-gray-700">Cost Breakdown</div>
                                </div>

                                <div class="mt-5 grid gap-6 sm:grid-cols-2 sm:items-center">
                                    {{-- Chart --}}
                                    <div class="flex items-center justify-center">
                                        <div class="relative h-[240px] w-[240px]">
                                            <canvas id="costBreakdownChartAlt" class="h-full w-full" aria-label="Cost breakdown chart" role="img"></canvas>
                                        </div>
                                    </div>

                                    {{-- Breakdown list --}}
                                    <div class="space-y-3">
                                        {{-- Fuel --}}
                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                                    <img src="{{ asset('icons/fuel.png') }}" alt="Fuel" class="h-11 w-10 object-contain">
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-gray-900">Fuel</div>
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-sm font-semibold text-gray-900">
                                                {{ $result['currency'] }} {{ $result['alternative']['fuel_cost'] ?? $result['fuel_cost'] }}
                                            </div>
                                        </div>

                                        {{-- Tolls (usually 0 in avoid-tolls, but keep generic) --}}
                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                                                    <img src="{{ asset('icons/tolls.png') }}" alt="Tolls" class="h-11 w-10 object-contain">
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-gray-900">Tolls</div>
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-sm font-semibold text-gray-900">
                                                {{ $result['toll_currency'] ?? $result['currency'] }} {{ $result['alternative']['toll_cost'] ?? 0 }}
                                            </div>
                                        </div>

                                        {{-- Vignette --}}
                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                                                    <img src="{{ asset('icons/vignette.png') }}" alt="Vignette" class="h-11 w-10 object-contain">
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-gray-900">Vignette</div>
                                                </div>
                                            </div>
                                            @php
                                                $hasVignetteUi = !empty($input['has_vignette']) || !empty($result['has_vignette']);
                                            @endphp

                                            <div class="shrink-0 text-sm font-semibold text-gray-900">
                                                {{ $hasVignetteUi ? 'Included' : 'Excluded' }}
                                            </div>

                                        </div>

                                        {{-- Total --}}
                                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4">
                                            <div class="flex min-w-0 items-center gap-4">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gray-800">
                                                    <img src="{{ asset('icons/total.png') }}" alt="Total" class="h-11 w-10 object-contain">
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-gray-900">Total</div>
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-sm font-semibold text-gray-900">
                                                {{ $result['currency'] }} {{ $result['alternative']['total_cost'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script type="application/json" id="cost-breakdown-data-alt">
                                {!! json_encode([
                                    'fuel' => (float) ($result['alternative']['fuel_cost'] ?? $result['fuel_cost'] ?? 0),
                                    'tolls' => (float) ($result['alternative']['toll_cost'] ?? 0),
                                    'vignette' => (float) ($result['alternative']['vignette_cost'] ?? $result['vignette_cost'] ?? 0),
                                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
                                </script>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Map card --}}
            <div class="rounded-2xl border border-white-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-3">
                    <div class="text-sm font-semibold text-gray-900">Route map</div>
                </div>

                <div class="px-0 pb-6">
                    <div
                        id="map"
                        class="w-full overflow-hidden rounded-2xl border border-gray-200"
                        style="height: 823px;"
                        data-here-key="{{ $hereApiKey }}"
                    ></div>

                    <script type="application/json" id="route-data">
                    {!! json_encode([
                        'main_polyline' => $result['polyline'] ?? null,
                        'alt_polyline' => $result['alternative']['polyline'] ?? null,
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
                    </script>
                </div>
            </div>
        </div>
    </div>

    {{-- HERE Maps JS (v3.1) --}}
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
    <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js" type="text/javascript" charset="utf-8"></script>
    <script src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js" type="text/javascript" charset="utf-8"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
    (function () {
        // ====== TABS (UI + optional map switching) ======
        const tabButtons = Array.from(document.querySelectorAll('.trip-tab-btn'));
        const tabPanels  = Array.from(document.querySelectorAll('.trip-tab-panel'));
        const tabsWrap = document.querySelector('[data-initial-tab]');
        const initialTab = (tabsWrap && tabsWrap.dataset.initialTab) ? tabsWrap.dataset.initialTab : 'main';

        function setActiveTab(name) {
            // prevent switching to disabled tab
            const targetBtn = tabButtons.find(b => b.dataset.tab === name);
            if (targetBtn && targetBtn.disabled) {
                name = 'main';
            }

            tabButtons.forEach(btn => {
                const isActive = btn.dataset.tab === name;
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');

                // Selected look (white pill)
                btn.classList.toggle('bg-white', isActive);
                btn.classList.toggle('text-gray-900', isActive);
                btn.classList.toggle('shadow-sm', isActive);

                // Unselected look
                btn.classList.toggle('bg-transparent', !isActive);
                btn.classList.toggle('text-gray-600', !isActive);

                // Disabled look
                if (btn.disabled) {
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });

            tabPanels.forEach(panel => {
                const show = panel.dataset.panel === name;
                panel.classList.toggle('hidden', !show);
            });

            if (window.__tripMap && typeof window.__tripMap.showRoute === 'function') {
                window.__tripMap.showRoute(name);
            }
        }

        if (tabButtons.length) {
            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.disabled) return;
                    setActiveTab(btn.dataset.tab);
                });
            });

            // initial state depends on whether alternative was requested
            setActiveTab(initialTab);
        }

        // ====== MAP ======
        const mapEl = document.getElementById('map');
        const dataEl = document.getElementById('route-data');

        if (mapEl && dataEl) {
            const hereApiKey = mapEl.dataset.hereKey;

            if (hereApiKey) {
                let routeData = {};
                try {
                    routeData = JSON.parse(dataEl.textContent || '{}');
                } catch (e) {
                    console.error('Failed to parse route-data JSON.', e);
                    routeData = {};
                }

                const mainPolyline = routeData.main_polyline;
                const altPolyline = routeData.alt_polyline;

                if (!mainPolyline) {
                    console.error('Main route polyline is missing.');
                } else {
                    const platform = new H.service.Platform({ apikey: hereApiKey });
                    const defaultLayers = platform.createDefaultLayers();

                    const map = new H.Map(
                        mapEl,
                        defaultLayers.vector.normal.map,
                        {
                            center: { lat: 52.52, lng: 13.405 },
                            zoom: 6,
                            pixelRatio: window.devicePixelRatio || 1
                        }
                    );

                    new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
                    H.ui.UI.createDefault(map, defaultLayers);
                    window.addEventListener('resize', () => map.getViewPort().resize());

                    const routeGroup = new H.map.Group();
                    map.addObject(routeGroup);

                    function addRoutePolyline(encodedPolyline, options = {}) {
                        const lineString = H.geo.LineString.fromFlexiblePolyline(encodedPolyline);

                        const style = {
                            lineWidth: options.lineWidth ?? 6,
                            strokeColor: options.strokeColor ?? 'rgba(17, 24, 39, 0.95)',
                            lineCap: 'round',
                            lineJoin: 'round'
                        };

                        const polyline = new H.map.Polyline(lineString, { style });
                        polyline.setZIndex(options.zIndex ?? 1000);

                        routeGroup.addObject(polyline);
                        return polyline;
                    }

                    const mainLine = addRoutePolyline(mainPolyline, {
                        lineWidth: 5,
                        strokeColor: 'rgba(107, 114, 128, 0.8)',
                        zIndex: 900
                    });

                    let altLine = null;
                    if (altPolyline) {
                        altLine = addRoutePolyline(altPolyline, {
                            lineWidth: 5,
                            strokeColor: 'rgba(107, 114, 128, 0.8)',
                            zIndex: 900
                        });
                    }

                    function fitToVisible() {
                        const bounds = routeGroup.getBoundingBox();
                        if (bounds) {
                            map.getViewModel().setLookAtData({
                                bounds,
                                padding: { top: 40, left: 40, bottom: 40, right: 40 }
                            });
                        }
                    }

                    window.__tripMap = {
                        showRoute: function (name) {
                            if (!altLine) {
                                mainLine.setVisibility(true);
                                fitToVisible();
                                return;
                            }

                            const showAlt = name === 'alt';
                            mainLine.setVisibility(!showAlt);
                            altLine.setVisibility(showAlt);

                            fitToVisible();
                        }
                    };

                    // set initial visibility based on initial tab
                    if (altLine) {
                        const showAltInitially = initialTab === 'alt';
                        mainLine.setVisibility(!showAltInitially);
                        altLine.setVisibility(showAltInitially);
                    }

                    fitToVisible();
                }
            }
        }

        // ====== CHARTS (Main + Alt) ======
        function initDoughnutChart(canvasId, dataScriptId) {
            const canvas = document.getElementById(canvasId);
            const dataEl = document.getElementById(dataScriptId);
            if (!canvas || !dataEl) return;

            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded.');
                return;
            }

            let costs = {};
            try {
                costs = JSON.parse(dataEl.textContent || '{}');
            } catch (e) {
                console.error('Failed to parse chart JSON: ' + dataScriptId, e);
                return;
            }

            const values = [
                Number(costs.fuel ?? 0),
                Number(costs.tolls ?? 0),
                Number(costs.vignette ?? 0),
            ];

            const isAllZero = values.every(v => v === 0);
            const safeValues = isAllZero ? [1, 0, 0] : values;

            if (canvas._tripCostChart) {
                canvas._tripCostChart.destroy();
            }

            canvas._tripCostChart = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['Fuel', 'Tolls', 'Vignette'],
                    datasets: [{
                        data: safeValues,
                        backgroundColor: ['#2563eb', '#f97316', '#a855f7'],
                        borderWidth: 0,
                        hoverOffset: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        initDoughnutChart('costBreakdownChartMain', 'cost-breakdown-data-main');
        initDoughnutChart('costBreakdownChartAlt', 'cost-breakdown-data-alt');
    })();
    </script>
@endsection
