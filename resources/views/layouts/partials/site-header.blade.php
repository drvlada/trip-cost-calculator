{{-- Header --}}
    <header class="relative z-50 border-b bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center px-4">
            {{-- Left: Logo --}}
            <a href="{{ url('/calculator') }}" class="flex items-center gap-2 font-semibold text-gray-900">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gray-900 text-white">
                    m
                </span>
                <span>TripCost.calc</span>
            </a>

            {{-- Center: Navigation --}}
            <nav class="hidden md:flex flex-1 items-center justify-center gap-8 text-sm font-medium">
                <a href="{{ url('/calculator') }}" class="text-gray-900 hover:text-gray-900">Calculator</a>

                {{-- History should be visible for both guests and authenticated users.
                     The page itself will show a login prompt for guests. --}}
                <a href="{{ url('/history') }}" class="text-gray-900 hover:text-gray-900">History</a>
            </nav>

            {{-- Right: Actions --}}
            <div class="ml-auto flex items-center gap-3">
                @auth
                <div class="relative" x-data="{ open: false }">
                    {{-- Profile icon button --}}
                    <button
                        type="button"
                        @click="open = !open"
                        @keydown.escape.window="open = false"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300"
                        aria-label="My account"
                     >
                        {{-- User icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="h-5 w-5">
                            <path d="M20 21a8 8 0 0 0-16 0"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="open"
                        x-cloak
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute right-0 mt-3 w-56 rounded-2xl border border-gray-200 bg-white shadow-lg"
                    >
                        <div class="px-4 py-3 text-sm font-semibold text-gray-900">
                            My Account
                        </div>

                        <div class="h-px bg-gray-100"></div>

                        <div class="py-2">
                           <a href="{{ url('/profile') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                {{-- gear icon --}}
                               <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                                <defs>
                                    <mask id="gear-hole-thin">
                                    <rect width="16" height="16" fill="white" />
                                    <circle cx="8" cy="8" r="3" fill="black" />
                                    </mask>
                                </defs>

                                <g fill="#444444" mask="url(#gear-hole-thin)">
                                    <circle cx="8" cy="8" r="4.5" />
                                    
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(45 8 8)" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(90 8 8)" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(135 8 8)" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(180 8 8)" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(225 8 8)" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(270 8 8)" />
                                    <rect x="7.5" y="1" width="1" height="3.5" rx="0.2" transform="rotate(315 8 8)" />
                                </g>
                                </svg>


                                <span>Settings</span>
                            </a>


                            <a href="{{ url('/history') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                {{-- history icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 1 0 9-9"/>
                                    <path d="M3 3v6h6"/>
                                    <path d="M12 7v5l3 3"/>
                                </svg>
                                <span>Trip History</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex w-full items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    {{-- logout icon --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <path d="M16 17l5-5-5-5"/>
                                        <path d="M21 12H9"/>
                                    </svg>
                                    <span>Log out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-100">
                    Log in
                </a>

                <a href="{{ route('register') }}"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                    Register
                </a>
            @endauth

            </div>
        </div>
    </header>