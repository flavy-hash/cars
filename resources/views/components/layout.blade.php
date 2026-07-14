@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — Velora' : 'Velora — Drive Elevated' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-wash font-sans">
    <div class="bg-aurora">
        <x-nav />
        <main>
            {{ $slot }}
        </main>
    </div>
    <x-footer />

    @if (session('status'))
        <div
            data-toast
            class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-ink-900 px-6 py-3 text-sm font-medium text-white shadow-xl shadow-ink-900/20"
        >
            {{ session('status') }}
        </div>
    @endif
</body>
</html>
