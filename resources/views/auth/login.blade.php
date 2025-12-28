@extends('layouts.guest')

@section('content')
    <div class="mx-auto w-full max-w-lg">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Welcome back</h1>
            <p class="mt-2 text-sm text-gray-600">Log in to access your trip history and settings.</p>
        </div>

        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-semibold text-gray-900">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-2 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-900
                                  outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                    @error('email')
                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-900">Password</label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <input type="password" name="password" required
                           class="mt-2 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm text-gray-900
                                  outline-none focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
                    @error('password')
                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300">
                    Remember me
                </label>

                <button type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-gray-900 px-4 py-3
                               text-sm font-semibold text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 focus:ring-offset-2">
                    Log in →
                </button>

                <p class="pt-2 text-center text-sm text-gray-600">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-gray-900 hover:underline">Register</a>
                </p>
            </form>
        </div>
    </div>
@endsection
