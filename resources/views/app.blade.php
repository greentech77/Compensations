<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#002640">

        <title inertia>{{ config('app.name', 'Compenzations') }}</title>

        {{-- Favicons. Prefer SVG (sharp at every size); ICO is generated from
             the same artwork only when needed for legacy browsers. --}}
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
        <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#002640">

        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap">

        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
