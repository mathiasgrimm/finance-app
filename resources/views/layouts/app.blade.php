<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen antialiased leading-none">
    <div id="app">

        {{-- TODO move this to a nav vue component        --}}
        <nav class="h-16 flex items-center justify-between bg-white">
            <div class="flex ml-8 justify-center items-center">
                <img src="{{ asset('/img/logo.svg') }}">
                <span class="ml-3 text-xl">
                    <span class="text-gray-800 font-semibold">Your</span><span class="text-primary font-medium">Balance</span>
                </span>
            </div>

            <div class="mr-8 flex items-center justify-between">
                <img src="{{ asset('/img/alarm.svg') }}">
                <img class="h-6 ml-4" src="{{ asset('/img/avatar.png') }}">
                <span class="ml-4 text-gray-600 font-bold text-xs">Molly Green<span>
            </div>
        </nav>

        @yield('content')
    </div>
</body>
</html>
