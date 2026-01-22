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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased bg-gray-100">

<div 
    x-data="{ sidebarOpen: false }"
    class="flex h-screen overflow-hidden"
>

    {{-- MOBILE OVERLAY --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
    ></div>

    {{-- SIDEBAR --}}
    @auth
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-blue-800 text-white flex flex-col transform
               -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out
               lg:static lg:inset-auto"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        x-data="{
            openMenu:
                @if (request()->routeIs('schools.*')) 'schools'
                @elseif (request()->routeIs('attendance.*')) 'attendance'
                @elseif (request()->routeIs('sports.*')) 'sports'
                @elseif (request()->routeIs('events.*')) 'events'
                @elseif (request()->routeIs('athletes.*')) 'athletes'
                @elseif (request()->routeIs('coaches.*')) 'coaches'
                @elseif (request()->routeIs('games.*')) 'games'
                @elseif (request()->routeIs('announcements.*')) 'announcements'
                @elseif (request()->routeIs('audit-logs.*')) 'logs'
                @elseif (request()->routeIs('users.*')) 'settings'
                @else null
                @endif
        }"
    >

        {{-- BRAND --}}
        <div class="p-6 font-bold text-xl border-b border-blue-700 flex-shrink-0">
            CEMS
            <p class="text-sm text-gray-300 mt-1">
                CAVRAA Event Monitoring System
            </p>
        </div>

        {{-- MENU --}}
        <nav class="mt-6 flex-1 overflow-y-auto">

            <a href="{{ route('dashboard') }}"
               class="block px-6 py-3 hover:bg-blue-700
               {{ request()->routeIs('dashboard') ? 'bg-blue-900 font-semibold' : '' }}">
                Dashboard
            </a>

            {{-- Attendance --}}
            <div>
                <button @click="openMenu = openMenu === 'attendance' ? null : 'attendance'"
                        class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700
                        {{ request()->routeIs('attendance.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Attendance
                    <span x-text="openMenu === 'attendance' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'attendance'" x-cloak class="pl-6 space-y-1">
                    <a href="{{ route('attendance.index') }}" class="block px-4 py-2 hover:bg-blue-600">
                        Manage Attendance
                    </a>
                </div>
            </div>

            {{-- Results --}}
            <div>
                <button @click="openMenu = openMenu === 'results' ? null : 'results'"
                        class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700">
                    Results / Awards
                    <span x-text="openMenu === 'results' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'results'" x-cloak class="pl-6 space-y-1">
                    <a href="{{ route('results.index') }}" class="block px-4 py-2 hover:bg-blue-600">All Results</a>
                    <a href="{{ route('results.create') }}" class="block px-4 py-2 hover:bg-blue-600">Award Medal</a>
                </div>
            </div>

            {{-- Participants --}}
            <div>
                <button @click="openMenu = openMenu === 'schools' ? null : 'schools'"
                        class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700">
                    Participants
                    <span x-text="openMenu === 'schools' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'schools'" x-cloak class="pl-6 space-y-1">
                    <a href="{{ route('schools.index') }}" class="block px-4 py-2 hover:bg-blue-600">All Participants</a>
                    <a href="{{ route('schools.create') }}" class="block px-4 py-2 hover:bg-blue-600">Add Participant</a>
                </div>
            </div>

            {{-- Sports --}}
            <div>
                <button @click="openMenu = openMenu === 'sports' ? null : 'sports'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('sports.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Sports
                    <span x-text="openMenu === 'sports' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'sports'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('sports.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('sports.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Sports
                    </a>
                    <a href="{{ route('sports.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('sports.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Sport
                    </a>
                </div>
            </div>

            {{-- Events --}}
            <div>
                <button @click="openMenu = openMenu === 'events' ? null : 'events'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('events.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Events
                    <span x-text="openMenu === 'events' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'events'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('events.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('events.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Events
                    </a>
                    <a href="{{ route('events.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('events.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Event
                    </a>
                </div>
            </div>

            {{-- Athletes --}}
            <div>
                <button @click="openMenu = openMenu === 'athletes' ? null : 'athletes'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('athletes.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Athletes
                    <span x-text="openMenu === 'athletes' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'athletes'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('athletes.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('athletes.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Athletes
                    </a>
                    <a href="{{ route('athletes.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('athletes.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Athlete
                    </a>
                </div>
            </div>

            {{-- Coaches --}}
            <div>
                <button @click="openMenu = openMenu === 'coaches' ? null : 'coaches'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('coaches.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Coaches
                    <span x-text="openMenu === 'coaches' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'coaches'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('coaches.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('coaches.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Coaches
                    </a>
                    <a href="{{ route('coaches.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('coaches.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Coach
                    </a>
                </div>
            </div>

            {{-- Officials --}}
            <div>
                <button @click="openMenu = openMenu === 'officials' ? null : 'officials'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('officials.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Officials
                    <span x-text="openMenu === 'officials' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'officials'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('officials.index') }}"
                    class="block px-4 py-2 rounded hover:bg-blue-600
                    {{ request()->routeIs('officials.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Officials
                    </a>
                    <a href="{{ route('officials.create') }}"
                    class="block px-4 py-2 rounded hover:bg-blue-600
                    {{ request()->routeIs('officials.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Official
                    </a>
                </div>
            </div>

            {{-- Games --}}
            <div>
                <button @click="openMenu = openMenu === 'games' ? null : 'games'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('games.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Games
                    <span x-text="openMenu === 'games' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'games'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('games.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('games.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Games
                    </a>
                    <a href="{{ route('games.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('games.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Game
                    </a>
                </div>
            </div>

            {{-- Announcements --}}
            <div>
                <button @click="openMenu = openMenu === 'announcements' ? null : 'announcements'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('announcements.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Announcements
                    <span x-text="openMenu === 'announcements' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'announcements'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('announcements.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('announcements.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Announcements
                    </a>
                    <a href="{{ route('announcements.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('announcements.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add Announcement
                    </a>
                </div>
            </div>

            {{-- Logs --}}
            <div>
                <button @click="openMenu = openMenu === 'logs' ? null : 'logs'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('audit-logs.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Logs
                    <span x-text="openMenu === 'logs' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'logs'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('audit-logs.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('audit-logs.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Logs
                    </a>
                </div>
            </div>

            {{-- Settings --}}
            <div>
                <button @click="openMenu = openMenu === 'settings' ? null : 'settings'"
                        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                        {{ request()->routeIs('users.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Settings
                    <span x-text="openMenu === 'settings' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'settings'" x-cloak class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('users.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('users.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        All Users
                    </a>
                    <a href="{{ route('users.create') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('users.create') ? 'bg-blue-700 font-semibold' : '' }}">
                        Add User
                    </a>
                    <a href="{{ route('settings.layout') }}"
                        class="block px-4 py-2 rounded hover:bg-blue-600
                        {{ request()->routeIs('settings.layout') ? 'bg-blue-700 font-semibold' : '' }}">
                        Layout Settings
                    </a>
                </div>
            </div>
        </nav>
    </aside>
    @endauth

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOP BAR --}}
        <header class="bg-white shadow flex items-center justify-between px-4 py-4">

            {{-- MOBILE MENU BUTTON --}}
            <button
                @click="sidebarOpen = true"
                class="lg:hidden text-gray-700 focus:outline-none"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <h1 class="text-lg font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h1>

            {{-- RIGHT --}}
            <div class="flex items-center gap-4">
                @auth
                    <span class="hidden sm:block text-gray-700">
                        Hello, {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-100">
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
