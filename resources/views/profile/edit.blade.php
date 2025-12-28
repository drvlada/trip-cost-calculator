@extends('layouts.guest')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Settings</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your profile, password, and account.</p>
        </div>
    </div>

    <div class="mt-8 space-y-6">
        <div class="rounded-xl bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-xl bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-xl bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
