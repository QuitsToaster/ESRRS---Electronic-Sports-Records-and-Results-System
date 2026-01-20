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
    <!-- Alpine.js for submenu toggling -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-blue-800 text-white flex-shrink-0" x-data="{ openMenu: null }">
        <div class="p-6 font-bold text-xl border-b border-blue-700">
            SDO
        </div>
        <nav class="mt-6 space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-900' : '' }}">
                Dashboard
            </a>

            {{-- Schools --}}
            <div>
                <button @click="openMenu === 'schools' ? openMenu = null : openMenu = 'schools'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('schools.*') ? 'bg-blue-900' : '' }}">
                    Schools
                    <span x-text="openMenu === 'schools' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'schools'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('schools.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('schools.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Schools</a>
                    <a href="{{ route('schools.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('schools.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add School</a>
                </div>
            </div>

            {{-- Sports --}}
            <div>
                <button @click="openMenu === 'sports' ? openMenu = null : openMenu = 'sports'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('sports.*') ? 'bg-blue-900' : '' }}">
                    Sports
                    <span x-text="openMenu === 'sports' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'sports'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('sports.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('sports.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Sports</a>
                    <a href="{{ route('sports.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('sports.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add Sport</a>
                </div>
            </div>

            {{-- Events --}}
            <div>
                <button @click="openMenu === 'events' ? openMenu = null : openMenu = 'events'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('events.*') ? 'bg-blue-900' : '' }}">
                    Events
                    <span x-text="openMenu === 'events' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'events'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('events.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('events.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Events</a>
                    <a href="{{ route('events.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('events.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add Event</a>
                </div>
            </div>

            {{-- Athletes --}}
            <div>
                <button @click="openMenu === 'athletes' ? openMenu = null : openMenu = 'athletes'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('athletes.*') ? 'bg-blue-900' : '' }}">
                    Athletes
                    <span x-text="openMenu === 'athletes' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'athletes'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('athletes.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('athletes.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Athletes</a>
                    <a href="{{ route('athletes.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('athletes.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add Athlete</a>
                </div>
            </div>

            {{-- Coaches --}}
            <div>
                <button @click="openMenu === 'coaches' ? openMenu = null : openMenu = 'coaches'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('coaches.*') ? 'bg-blue-900' : '' }}">
                    Coaches
                    <span x-text="openMenu === 'coaches' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'coaches'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('coaches.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('coaches.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Coaches</a>
                    <a href="{{ route('coaches.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('coaches.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add Coach</a>
                </div>
            </div>

            {{-- Games --}}
            <div>
                <button @click="openMenu === 'games' ? openMenu = null : openMenu = 'games'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('games.*') ? 'bg-blue-900' : '' }}">
                    Games
                    <span x-text="openMenu === 'games' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'games'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('games.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('games.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Games</a>
                    <a href="{{ route('games.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('games.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add Game</a>
                </div>
            </div>

            {{-- Announcements --}}
            <div>
                <button @click="openMenu === 'announcements' ? openMenu = null : openMenu = 'announcements'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('announcements.*') ? 'bg-blue-900' : '' }}">
                    Announcements
                    <span x-text="openMenu === 'announcements' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'announcements'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('announcements.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('announcements.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Announcements</a>
                    <a href="{{ route('announcements.create') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('announcements.create') ? 'bg-blue-700 font-semibold' : 'text-white' }}">Add Announcement</a>
                </div>
            </div>

            {{-- Logs --}}
            <div>
                <button @click="openMenu === 'logs' ? openMenu = null : openMenu = 'logs'"
                        class="w-full text-left flex justify-between items-center px-6 py-3 hover:bg-blue-700 rounded {{ request()->routeIs('audit-logs.*') ? 'bg-blue-900' : '' }}">
                    Logs
                    <span x-text="openMenu === 'logs' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'logs'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('audit-logs.index') }}" class="block px-4 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('audit-logs.index') ? 'bg-blue-700 font-semibold' : 'text-white' }}">All Logs</a>
                </div>
            </div>

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
