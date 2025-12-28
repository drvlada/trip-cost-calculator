@extends('layouts.guest')

@section('content')
<div class="mx-auto w-full max-w-6xl pb-16">

    {{-- Page header --}}
    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                Trip History
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Manage your saved trip calculations.
            </p>
        </div>
    </div>

    @guest
        <div class="mt-10 rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm">
            <div class="text-sm font-semibold text-gray-900">Login required</div>
            <p class="mt-2 text-sm text-gray-600">
                To use trip history, please sign in to your account.
            </p>

            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                    Log in
                </a>

                <a href="{{ route('calculator') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">
                    Open Calculator
                </a>
            </div>
        </div>
    @endguest

    {{-- Auth --}}
    @auth
        @if($trips->isEmpty())
            <div class="mt-10 rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                <p class="text-sm text-gray-600">
                    You haven’t saved any trips yet.
                </p>
                <a href="{{ route('calculator') }}"
                   class="mt-4 inline-flex rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                    Open Calculator
                </a>
            </div>
        @else
            <div id="tripList" class="mt-8 space-y-4">
                @foreach($trips as $trip)
                    <div
                        class="trip-item rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                        data-search="{{ strtolower($trip->from.' '.$trip->to.' '.$trip->fuel_type) }}"
                    >
                        {{-- Top row: cities (left) + price/actions (right) --}}
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            {{-- LEFT: cities + badges --}}
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="truncate text-lg font-bold text-gray-900">
                                        {{ $trip->from }}
                                        <span class="mx-2 text-gray-400">→</span>
                                        {{ $trip->to }}
                                    </h2>

                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ strtolower($trip->fuel_type) }}
                                    </span>

                                    @if($trip->has_vignette)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            Vignette
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta --}}
                                <div class="mt-2 flex flex-wrap items-center gap-6 text-sm text-gray-600">
                                    <span>{{ $trip->created_at->format('Y-m-d') }}</span>
                                    <span>{{ (int) $trip->distance_km }} km</span>
                                </div>
                            </div>

                            {{-- RIGHT: price + actions --}}
                            <div class="flex shrink-0 items-center justify-end gap-3">
                                <div class="text-lg font-bold text-gray-900">
                                    €{{ number_format($trip->total_cost, 2) }}
                                </div>

                                <a
                                    href="{{ route('calculator.fromHistory', $trip) }}"
                                    class="inline-flex h-10 items-center justify-center rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-900 hover:bg-gray-50"
                                >
                                    View
                                </a>

                                <form
                                    action="{{ route('history.destroy', $trip) }}"
                                    method="POST"
                                    class="delete-trip-form"
                                    data-trip-title="{{ $trip->from }} → {{ $trip->to }}"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="button"
                                        class="open-delete-modal inline-flex h-10 w-10 items-center justify-center rounded-xl text-red-600 hover:bg-red-50"
                                        aria-label="Delete"
                                        title="Delete"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 6V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19 6L18 20C17.94 21.1046 17.1046 22 16 22H8C6.89543 22 6.06 21.1046 6 20L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Search logic --}}
            <script>
                (function () {
                    const input = document.getElementById('tripSearch');
                    const list = document.getElementById('tripList');
                    if (!input || !list) return;

                    const items = Array.from(list.querySelectorAll('.trip-item'));
                    input.addEventListener('input', () => {
                        const q = (input.value || '').toLowerCase().trim();
                        items.forEach((el) => {
                            const hay = (el.getAttribute('data-search') || '');
                            el.style.display = hay.includes(q) ? '' : 'none';
                        });
                    });
                })();
            </script>
        @endif
    @endauth

        {{-- Delete Modal --}}
        <div
            id="deleteModal"
            class="fixed inset-0 z-50 hidden"
            aria-labelledby="deleteModalTitle"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div id="deleteModalBackdrop" class="absolute inset-0 bg-black/40"></div>

            {{-- Panel --}}
            <div class="relative mx-auto flex min-h-full max-w-lg items-center p-4">
                <div class="w-full rounded-2xl bg-white shadow-xl">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-4">
                        <div>
                            <h2 id="deleteModalTitle" class="text-base font-semibold text-gray-900">
                                Delete trip?
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                This action cannot be undone.
                            </p>
                        </div>

                        <button
                            type="button"
                            id="deleteModalClose"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 hover:bg-gray-50"
                            aria-label="Close"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="px-6 py-4">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                            <div class="font-semibold text-gray-900">Trip:</div>
                            <div id="deleteModalTripTitle" class="mt-1 text-gray-700">—</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                        <button
                            type="button"
                            id="deleteModalCancel"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            id="deleteModalConfirm"
                            class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // ---------- Delete modal logic ----------
            const modal = document.getElementById('deleteModal');
            const modalBackdrop = document.getElementById('deleteModalBackdrop');
            const modalClose = document.getElementById('deleteModalClose');
            const modalCancel = document.getElementById('deleteModalCancel');
            const modalConfirm = document.getElementById('deleteModalConfirm');
            const modalTripTitle = document.getElementById('deleteModalTripTitle');

            let activeForm = null;

            function openDeleteModal(form) {
                activeForm = form;
                modalTripTitle.textContent = form.dataset.tripTitle || '—';

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                // Optional: focus confirm button
                modalConfirm.focus();
            }

            function closeDeleteModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                activeForm = null;
            }

            document.querySelectorAll('.open-delete-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form.delete-trip-form');
                    if (form) openDeleteModal(form);
                });
            });

            modalBackdrop.addEventListener('click', closeDeleteModal);
            modalClose.addEventListener('click', closeDeleteModal);
            modalCancel.addEventListener('click', closeDeleteModal);

            modalConfirm.addEventListener('click', () => {
                if (activeForm) activeForm.submit();
            });

            // Close on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            });
        </script>

</div>
@endsection
