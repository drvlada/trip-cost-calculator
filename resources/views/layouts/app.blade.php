<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TripCosts') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900">
    @include('layouts.partials.site-header')

    @isset($header)
        <div class="mx-auto max-w-6xl px-4 pt-10">
            {{ $header }}
        </div>
    @endisset

    <main class="mx-auto w-full max-w-6xl px-4 py-10">
        {{ $slot }}
    </main>

    @include('layouts.partials.site-footer')
</body>
</html>
