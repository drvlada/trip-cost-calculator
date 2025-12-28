@extends('layouts.guest')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Overview of your account.
            </p>
        </div>
    </div>

    <div class="mt-8">
        <div class="rounded-xl bg-white p-6 shadow sm:rounded-lg">
            <p class="text-gray-900 font-medium">
                You’re logged in!
            </p>
        </div>
    </div>
@endsection
