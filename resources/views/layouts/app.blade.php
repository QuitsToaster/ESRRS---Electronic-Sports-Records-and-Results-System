<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CAVRAA Monitoring System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-blue-800 text-white flex-shrink-0">
        <div class="p-6 font-bold text-xl border-b border-blue-700">
            SDO
        </div>
        <nav class="mt-6">
            <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-900' : '' }}">Dashboard</a>
            <a href="{{ route('schools.index') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('schools.*') ? 'bg-blue-900' : '' }}">Schools</a>
            <a href="{{ route('sports.index') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('sports.*') ? 'bg-blue-900' : '' }}">Sports</a>
            <a href="{{ route('events.index') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('events.*') ? 'bg-blue-900' : '' }}">Events</a>
            <a href="{{ route('athletes.index') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('athletes.*') ? 'bg-blue-900' : '' }}">Athletes</a>
            <a href="{{ route('coaches.index') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('coaches.*') ? 'bg-blue-900' : '' }}">Coaches</a>
            <a href="{{ route('games.index') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('coaches.*') ? 'bg-blue-900' : '' }}">Games</a>
        </nav>
    </aside>

    {{-- MAIN CONTENT AREA --}}
    <div class="flex-1 flex flex-col overflow-auto">

        {{-- TOP BAR --}}
        <header class="bg-white shadow flex items-center justify-between px-6 py-4">
            <div class="text-lg font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700">Hello, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                </form>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-6 bg-gray-100">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="bg-white shadow p-4 text-center text-gray-600">
            © {{ date('Y') }} CAVRAA Monitoring System - SDO Santiago City
        </footer>

    </div>
</div>

</body>
</html>
