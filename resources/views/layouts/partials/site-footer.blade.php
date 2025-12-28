  {{-- Footer --}}
    <footer class="border-t bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2 font-semibold">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gray-900 text-white">m</span>
                        <span>TripCost.calc</span>
                    </div>
                    <p class="mt-3 text-sm text-gray-600">
                        Helping travelers estimate costs, save money, and split bills efficiently.
                    </p>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-900">Resources</div>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-gray-900">Fuel Price Index</a></li>
                        <li><a href="#" class="hover:text-gray-900">Toll Rates</a></li>
                        <li><a href="#" class="hover:text-gray-900">Vignette Guide</a></li>
                    </ul>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-900">Legal</div>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-gray-900">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-gray-900">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-gray-900">Disclaimer</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 border-t pt-6 text-center text-xs text-gray-500">
                © {{ date('Y') }} TripCosts. Estimates may vary from actual costs.
            </div>
        </div>
    </footer>
</body>