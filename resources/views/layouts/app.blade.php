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

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR (only for logged-in users/admins) --}}
@auth
<aside
    class="w-64 bg-blue-800 text-white flex-shrink-0 flex flex-col"
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
    <div class="p-6 font-bold text-xl border-b border-blue-700 flex-shrink-0">
        CEMS
        <p class="text-sm text-gray-300 mt-1">
            CAVRAA Event Monitoring System
        </p>
    </div>

    {{-- Scrollable Menu --}}
    <nav class="mt-6 flex-1 overflow-y-auto px-0">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="block px-6 py-3 rounded hover:bg-blue-700
           {{ request()->routeIs('dashboard') ? 'bg-blue-900 font-semibold' : '' }}">
            Dashboard
        </a>


        {{-- Attendance --}}
        <div x-data="{ openMenu: {{ request()->routeIs('attendance.*') ? '\'attendance\'' : 'null' }} }">
            <button @click="openMenu = openMenu === 'attendance' ? null : 'attendance'"
                    class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                    {{ request()->routeIs('attendance.*') ? 'bg-blue-900 font-semibold' : '' }}">
                Attendance
                <span x-text="openMenu === 'attendance' ? '▾' : '▸'"></span>
            </button>
            <div x-show="openMenu === 'attendance'" x-cloak class="pl-6 mt-1 space-y-1">
                <a href="{{ route('attendance.index') }}"
                class="block px-4 py-2 rounded hover:bg-blue-600
                {{ request()->routeIs('attendance.index') ? 'bg-blue-700 font-semibold' : '' }}">
                    Manage Attendance
                </a>
            </div>
        </div>

        {{-- Results / Awards --}}
        <div x-data="{ openMenu: {{ request()->routeIs('results.*') ? '\'results\'' : 'null' }} }">
            <button @click="openMenu = openMenu === 'results' ? null : 'results'"
                    class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                    {{ request()->routeIs('results.*') ? 'bg-blue-900 font-semibold' : '' }}">
                Results / Awards
                <span x-text="openMenu === 'results' ? '▾' : '▸'"></span>
            </button>
            <div x-show="openMenu === 'results'" x-cloak class="pl-6 mt-1 space-y-1">
                <a href="{{ route('results.index') }}"
                class="block px-4 py-2 rounded hover:bg-blue-600
                {{ request()->routeIs('results.index') ? 'bg-blue-700 font-semibold' : '' }}">
                    All Results
                </a>
                <a href="{{ route('results.create') }}"
                class="block px-4 py-2 rounded hover:bg-blue-600
                {{ request()->routeIs('results.create') ? 'bg-blue-700 font-semibold' : '' }}">
                    Award Medal
                </a>
            </div>
        </div>

        {{-- Schools --}}
        <div x-data="{ openMenu: {{ request()->routeIs('schools.*') ? '\'schools\'' : 'null' }} }">
            <button @click="openMenu = openMenu === 'schools' ? null : 'schools'"
                    class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                    {{ request()->routeIs('schools.*') ? 'bg-blue-900 font-semibold' : '' }}">
                Participants
                <span x-text="openMenu === 'schools' ? '▾' : '▸'"></span>
            </button>
            <div x-show="openMenu === 'schools'" x-cloak class="pl-6 mt-1 space-y-1">
                <a href="{{ route('schools.index') }}"
                class="block px-4 py-2 rounded hover:bg-blue-600
                {{ request()->routeIs('schools.index') ? 'bg-blue-700 font-semibold' : '' }}">
                    All Participants
                </a>
                <a href="{{ route('schools.create') }}"
                class="block px-4 py-2 rounded hover:bg-blue-600
                {{ request()->routeIs('schools.create') ? 'bg-blue-700 font-semibold' : '' }}">
                    Add Participants
                </a>
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
                </div>
            </div>
        </nav>
    </aside>
@endauth


    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-auto">

        {{-- TOP BAR --}}
        <header class="bg-white shadow flex items-center justify-between px-6 py-4 flex-shrink-0">
            <div class="text-lg font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </div>

            <div class="flex items-center space-x-4">

                {{-- Greeting (only if logged in) --}}
                @auth
                    <span class="text-gray-700">
                        Hello, {{ auth()->user()->name }}
                    </span>

                    {{-- Settings Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring">
                            <!-- Gear Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-wide-connected" viewBox="0 0 16 16">
                            <path d="M7.068.727c.243-.97 1.62-.97 1.864 0l.071.286a.96.96 0 0 0 1.622.434l.205-.211c.695-.719 1.888-.03 1.613.931l-.08.284a.96.96 0 0 0 1.187 1.187l.283-.081c.96-.275 1.65.918.931 1.613l-.211.205a.96.96 0 0 0 .434 1.622l.286.071c.97.243.97 1.62 0 1.864l-.286.071a.96.96 0 0 0-.434 1.622l.211.205c.719.695.03 1.888-.931 1.613l-.284-.08a.96.96 0 0 0-1.187 1.187l.081.283c.275.96-.918 1.65-1.613.931l-.205-.211a.96.96 0 0 0-1.622.434l-.071.286c-.243.97-1.62.97-1.864 0l-.071-.286a.96.96 0 0 0-1.622-.434l-.205.211c-.695.719-1.888.03-1.613-.931l.08-.284a.96.96 0 0 0-1.186-1.187l-.284.081c-.96.275-1.65-.918-.931-1.613l.211-.205a.96.96 0 0 0-.434-1.622l-.286-.071c-.97-.243-.97-1.62 0-1.864l.286-.071a.96.96 0 0 0 .434-1.622l-.211-.205c-.719-.695-.03-1.888.931-1.613l.284.08a.96.96 0 0 0 1.187-1.186l-.081-.284c-.275-.96.918-1.65 1.613-.931l.205.211a.96.96 0 0 0 1.622-.434zM12.973 8.5H8.25l-2.834 3.779A4.998 4.998 0 0 0 12.973 8.5m0-1a4.998 4.998 0 0 0-7.557-3.779l2.834 3.78zM5.048 3.967l-.087.065zm-.431.355A4.98 4.98 0 0 0 3.002 8c0 1.455.622 2.765 1.615 3.678L7.375 8zm.344 7.646.087.065z"/>
                            </svg>
                        </button>

                        {{-- Modal --}}
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg w-96 p-6 shadow-lg relative">
                                <h2 class="text-xl font-semibold text-gray-800 mb-4">My Profile</h2>

                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('PUT')

                                    {{-- Name --}}
                                    <div class="mb-3">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                                        <input type="text" name="name" required
                                               value="{{ old('name', auth()->user()?->name) }}"
                                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                                    </div>

                                    {{-- Email --}}
                                    <div class="mb-3">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" required
                                               value="{{ old('email', auth()->user()?->email) }}"
                                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                                    </div>

                                    {{-- Password --}}
                                    <div class="mb-3">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password (leave blank to keep current)</label>
                                        <input type="password" name="password"
                                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
                                        <input type="password" name="password_confirmation"
                                               class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="open = false"
                                                class="px-4 py-2 rounded border border-gray-400 text-gray-700 hover:bg-gray-100">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="px-5 py-2 rounded bg-green-600 text-white font-semibold hover:bg-green-700 shadow">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                @endauth

            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-6 bg-gray-100 overflow-auto">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="bg-white shadow p-4 text-center text-gray-600 flex-shrink-0">
            © {{ date('Y') }} CAVRAA Monitoring System - SDO Santiago City
        </footer>

    </div>
</div>

</body>
</html>
