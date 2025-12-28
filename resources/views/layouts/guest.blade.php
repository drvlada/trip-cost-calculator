<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TripCost.calc</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-gray-100 text-gray-900 antialiased">
    @include('layouts.partials.site-header')

    {{-- Main --}}
    <main class="flex-1 py-10">
        <div class="mx-auto max-w-6xl px-4">
            @yield('content')
        </div>
    </main>

    @include('layouts.partials.site-footer')
</body>
</html>
