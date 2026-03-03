<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <title>{{ config('app.name', 'ESRRS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Ensure loader covers scrollbar too */
html, body {
    overflow: hidden;
}

[x-data][x-show="loading"] {
    pointer-events: all;
}

    </style>
</head>

<body class="font-sans antialiased bg-gray-100">

<!-- {{-- FULL PAGE LOADER --}}
<div
    x-data="{ loading: true }"
    x-init="
        window.addEventListener('load', () => {
            setTimeout(() => loading = false, 500);
        });

        document.addEventListener('alpine:initializing', () => {
            loading = true;
        });
    "
    x-show="loading"
    x-transition.opacity.duration.300ms
    style="display: none"
    class="fixed inset-0 z-[10000] flex items-center justify-center bg-white"
>
    <div class="flex flex-col items-center gap-4">
        {{-- Spinner --}}
        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
        {{-- Loading Text --}}
        <p class="text-sm font-medium text-gray-600 tracking-wide">
            Loading ESRRS…
        </p>
    </div>
</div> -->

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loader = document.querySelector('[x-data="{ loading: true }"]') || document.querySelector('[x-data]');
    
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');

            // Ignore anchors, javascript, new tabs
            if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank') return;

            // Show loader
            if (loader && loader.__x) {
                loader.__x.$data.loading = true;
            }
        });
    });
});
</script>



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

    @php
    $access = auth()->user()->access ?? [];
    @endphp

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
                @elseif (request()->routeIs('gallery.*')) 'gallery'
                @elseif (request()->routeIs('announcements.*')) 'announcements'
                @elseif (request()->routeIs('audit-logs.*')) 'logs'
                @elseif (request()->routeIs('users.*')) 'settings'
                @else null
                @endif
        }"
    >

        {{-- BRAND --}}
@php
    $adminLogo = \App\Models\LayoutSetting::where('key', 'admin_sidebar_logo')->first();
@endphp

<div class="p-6 border-b border-blue-700 flex flex-col items-center gap-2 flex-shrink-0">

    @if($adminLogo && $adminLogo->value)
        <img
            src="{{ asset('storage/' . $adminLogo->value) }}"
            alt="Admin Logo"
            class="h-14 object-contain"
        >
    @else
        <span class="font-bold text-xl">ESRRS</span>
    @endif

    <p class="text-xs text-gray-300 text-center">
        ESRRS - Electronic Sports Records and Results System
    </p>
</div>

{{-- SIDEBAR SEARCH --}}
<div
    class="px-4 mt-4"
    x-data="{ query: '' }"
    x-init="$watch('query', value => {
        document.querySelectorAll('[data-nav]').forEach(el => {
            el.style.display = el.innerText.toLowerCase().includes(value.toLowerCase())
                ? ''
                : 'none';
        });
    })"
>
    <input
        type="text"
        x-model="query"
        placeholder="Search menu..."
        class="w-full px-3 py-2 rounded bg-blue-700 text-white placeholder-blue-200
               focus:outline-none focus:ring-2 focus:ring-blue-400"
    >
</div>


        {{-- MENU --}}
        <nav class="mt-6 flex-1 overflow-y-auto">

            @if(in_array('dashboard', $access))
    <a data-nav
       href="{{ route('dashboard') }}"
       class="block px-6 py-3 hover:bg-blue-700
       {{ request()->routeIs('dashboard') ? 'bg-blue-900 font-semibold' : '' }}">
        Dashboard
    </a>
@endif


           {{-- Attendance --}}
        @if(in_array('attendance', $access))
            <div>
                <button data-nav @click="openMenu = openMenu === 'attendance' ? null : 'attendance'"
                        class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700
                        {{ request()->routeIs('attendance.*') ? 'bg-blue-900 font-semibold' : '' }}">
                    Attendance
                    <span x-text="openMenu === 'attendance' ? '▾' : '▸'"></span>
                </button>
                <div x-show="openMenu === 'attendance'" x-cloak class="pl-6 space-y-1">
                    <a href="{{ route('attendance.index') }}"
                       class="block px-4 py-2 rounded hover:bg-blue-600
                       {{ request()->routeIs('attendance.index') ? 'bg-blue-700 font-semibold' : '' }}">
                        Manage Attendance
                    </a>
                </div>
            </div>
        @endif
{{-- Results --}}
@if(in_array('results', $access))
    <div x-data="{ isOpen: {{ request()->routeIs('results_divisions.*') ? 'true' : 'false' }} }">
        <button data-nav @click="isOpen = !isOpen"
                class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700
                {{ request()->routeIs('results_divisions.*') ? 'bg-blue-900 font-semibold' : '' }}">
            Results & Awards
            <span x-text="isOpen ? '▾' : '▸'"></span>
        </button>

        <div x-show="isOpen" x-cloak class="pl-6 mt-1 space-y-1">

            {{-- Official Results: only for roles that are NOT Tournament Secretary --}}
            @if(auth()->check() && !in_array(optional(auth()->user()->role)->name, ['Tournament Secretary', 'Tournament Manager']))
                <a href="{{ route('results_divisions.index') }}"
                   class="block px-4 py-2 rounded hover:bg-blue-600
                   {{ request()->routeIs('results_divisions.index') ? 'bg-blue-700 font-semibold' : '' }}">
                    Official Results
                </a>
            @endif

            {{-- Award Medal: visible to all users with 'results' access --}}
            <a href="{{ route('results_divisions.create') }}"
               class="block px-4 py-2 rounded hover:bg-blue-600
               {{ request()->routeIs('results_divisions.create') ? 'bg-blue-700 font-semibold' : '' }}">
                Award Medal
            </a>
        </div>
    </div>
@endif


{{-- Event --}}
@if(in_array('events', $access))
    <div x-data="{ isOpen: {{ request()->routeIs(['events.*']) ? 'true' : 'false' }} }">
        <button data-nav @click="isOpen = !isOpen"
                class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700
                {{ request()->routeIs(['events.*']) ? 'bg-blue-900 font-semibold' : '' }}">
            Events Schedule
            <span x-text="isOpen ? '▾' : '▸'"></span>
        </button>

        <div x-show="isOpen" x-cloak class="pl-6 mt-1 space-y-1">

            {{-- Submenu --}}
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
@endif


@if(in_array('divisions', $access))
<div>
    <button data-nav @click="openMenu = openMenu === 'divisions' ? null : 'divisions'"
            class="w-full flex justify-between items-center px-6 py-3 hover:bg-blue-700
            {{ request()->routeIs('divisions.*') ? 'bg-blue-900 font-semibold' : '' }}">
        Divisions
        <span x-text="openMenu === 'divisions' ? '▾' : '▸'"></span>
    </button>
    <div x-show="openMenu === 'divisions'" x-cloak class="pl-6 mt-1 space-y-1">
        <a href="{{ route('divisions.index') }}"
           class="block px-4 py-2 rounded hover:bg-blue-600
           {{ request()->routeIs('divisions.index') ? 'bg-blue-700 font-semibold' : '' }}">
            All Divisions
        </a>
        <a href="{{ route('divisions.create') }}"
           class="block px-4 py-2 rounded hover:bg-blue-600
           {{ request()->routeIs('divisions.create') ? 'bg-blue-700 font-semibold' : '' }}">
            Add Division
        </a>
    </div>
</div>
@endif


            @if(in_array('participants', $access))
{{-- Teams / Schools --}}
<div
    x-data="{ isActive: {{ request()->routeIs('schools.*') ? 'true' : 'false' }} }"
    x-init="if (isActive) openMenu = 'schools'"
>
    <button
        data-nav @click="openMenu = openMenu === 'schools' ? null : 'schools'"
        class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
        {{ request()->routeIs('schools.*') ? 'bg-blue-900 font-semibold' : '' }}"
    >
        Schools
        <span x-text="openMenu === 'schools' ? '▾' : '▸'"></span>
    </button>

    <div x-show="openMenu === 'schools'" x-cloak class="pl-6 mt-1 space-y-1">
        <a href="{{ route('schools.index') }}"
           class="block px-4 py-2 rounded hover:bg-blue-600
           {{ request()->routeIs('schools.index') ? 'bg-blue-700 font-semibold' : '' }}">
            All Schools
        </a>

        <a href="{{ route('schools.create') }}"
           class="block px-4 py-2 rounded hover:bg-blue-600
           {{ request()->routeIs('schools.create') ? 'bg-blue-700 font-semibold' : '' }}">
            Add Schools
        </a>
    </div>
</div>
@endif


            @if(in_array('sports', $access))
<div>
    <button data-nav @click="openMenu = openMenu === 'sports' ? null : 'sports'"
            class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
            {{ request()->routeIs('sports.*') || request()->routeIs('para-sports.*') ? 'bg-blue-900 font-semibold' : '' }}">
        Sports
        <span x-text="openMenu === 'sports' ? '▾' : '▸'"></span>
    </button>

    <div x-show="openMenu === 'sports'" x-cloak class="pl-6 mt-1 space-y-1">

        {{-- REGULAR SPORTS --}}
        <p class="text-xs text-blue-300 uppercase px-4 mt-2">Regular Sports</p>

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

        {{-- PARA GAMES --}}
        <p class="text-xs text-blue-300 uppercase px-4 mt-3">Para Games</p>

        <a href="{{ route('para-sports.index') }}"
           class="block px-4 py-2 rounded hover:bg-blue-600
           {{ request()->routeIs('para-sports.index') ? 'bg-blue-700 font-semibold' : '' }}">
            All Para Games
        </a>

        <a href="{{ route('para-sports.create') }}"
           class="block px-4 py-2 rounded hover:bg-blue-600
           {{ request()->routeIs('para-sports.create') ? 'bg-blue-700 font-semibold' : '' }}">
            Add Para Game
        </a>
    </div>
</div>
@endif


       @if(in_array('athletes', $access))
    {{-- Athletes --}}
    <div>
        <button data-nav 
                @click="openMenu = openMenu === 'athletes' ? null : 'athletes'"
                class="w-full flex justify-between items-center px-6 py-3 rounded hover:bg-blue-700
                {{ request()->routeIs('athletes.*') ? 'bg-blue-900 font-semibold' : '' }}">
            Athletes
            <span x-text="openMenu === 'athletes' ? '▾' : '▸'"></span>
        </button>

        <div x-show="openMenu === 'athletes'" x-cloak class="pl-6 mt-1 space-y-1">

            {{-- Always visible --}}
            <a href="{{ route('athletes.index') }}"
               class="block px-4 py-2 rounded hover:bg-blue-600
               {{ request()->routeIs('athletes.index') ? 'bg-blue-700 font-semibold' : '' }}">
                All Athletes
            </a>

            {{-- Hidden if role contains "Division Official" --}}
            @if(!Str::contains(strtolower(auth()->user()->role), 'division official'))
                <a href="{{ route('athletes.create') }}"
                   class="block px-4 py-2 rounded hover:bg-blue-600
                   {{ request()->routeIs('athletes.create') ? 'bg-blue-700 font-semibold' : '' }}">
                    Add Athlete
                </a>
            @endif

        </div>
    </div>
@endif

            @if(in_array('coaches', $access))
            {{-- Coaches --}}
            <div>
                <button data-nav @click="openMenu = openMenu === 'coaches' ? null : 'coaches'"
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
            @endif



            @if(in_array('announcements', $access))
            {{-- Announcements --}}
            <div>
                <button data-nav @click="openMenu = openMenu === 'announcements' ? null : 'announcements'"
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
            @endif

            @if(in_array('logs', $access))
            {{-- Logs --}}
            <div>
                <button data-nav @click="openMenu = openMenu === 'logs' ? null : 'logs'"
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
            @endif

            @if(in_array('settings', $access))
            {{-- Settings --}}
            <div>
                <button data-nav @click="openMenu = openMenu === 'settings' ? null : 'settings'"
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
            @endif
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
        {{-- Greeting (desktop only) --}}
        <span class="hidden sm:block text-gray-700">
            Hello, {{ auth()->user()->name }}
        </span>
    {{-- SETTINGS WRAPPER --}}
<div class="relative flex items-center">

    {{-- SETTINGS ICON (TOP SVG / KEBAB MENU) --}}
    <button onclick="toggleSettingsDropdown()"
            class="p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-wide-connected" viewBox="0 0 16 16">
  <path d="M7.068.727c.243-.97 1.62-.97 1.864 0l.071.286a.96.96 0 0 0 1.622.434l.205-.211c.695-.719 1.888-.03 1.613.931l-.08.284a.96.96 0 0 0 1.187 1.187l.283-.081c.96-.275 1.65.918.931 1.613l-.211.205a.96.96 0 0 0 .434 1.622l.286.071c.97.243.97 1.62 0 1.864l-.286.071a.96.96 0 0 0-.434 1.622l.211.205c.719.695.03 1.888-.931 1.613l-.284-.08a.96.96 0 0 0-1.187 1.187l.081.283c.275.96-.918 1.65-1.613.931l-.205-.211a.96.96 0 0 0-1.622.434l-.071.286c-.243.97-1.62.97-1.864 0l-.071-.286a.96.96 0 0 0-1.622-.434l-.205.211c-.695.719-1.888.03-1.613-.931l.08-.284a.96.96 0 0 0-1.186-1.187l-.284.081c-.96.275-1.65-.918-.931-1.613l.211-.205a.96.96 0 0 0-.434-1.622l-.286-.071c-.97-.243-.97-1.62 0-1.864l.286-.071a.96.96 0 0 0 .434-1.622l-.211-.205c-.719-.695-.03-1.888.931-1.613l.284.08a.96.96 0 0 0 1.187-1.186l-.081-.284c-.275-.96.918-1.65 1.613-.931l.205.211a.96.96 0 0 0 1.622-.434zM12.973 8.5H8.25l-2.834 3.779A4.998 4.998 0 0 0 12.973 8.5m0-1a4.998 4.998 0 0 0-7.557-3.779l2.834 3.78zM5.048 3.967l-.087.065zm-.431.355A4.98 4.98 0 0 0 3.002 8c0 1.455.622 2.765 1.615 3.678L7.375 8zm.344 7.646.087.065z"/>
</svg>
    </button>

    {{-- DROPDOWN --}}
    <div id="settingsDropdown"
         class="hidden absolute right-0 top-full mt-2 w-44
                bg-white border rounded-lg shadow-lg z-50">

        <button onclick="openProfileModal()"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Profile
        </button>

        <button onclick="openSwitchAccountModal()"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Switch Account
        </button>
    </div>

</div>



        <form method="POST" action="{{ route('logout') }}">
            @csrf
            {{-- DESKTOP LOGOUT (text) --}}
            <button type="submit"
                    class="hidden sm:inline-block text-red-600 hover:text-red-800 font-medium">
                Logout
            </button>

            {{-- MOBILE LOGOUT (icon) --}}
            <button type="submit"
                    class="sm:hidden text-red-600 hover:text-red-800 focus:outline-none"
                    title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                </svg>
            </button>
        </form>
    @endauth
</div>


        </header>

{{-- SETTINGS MODAL --}}
<div id="settingsModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 relative w-96">

        {{-- Close button --}}
        <button onclick="closeSettingsModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">
            &times;
        </button>

        {{-- Header --}}
        <h3 id="settingsTitle" class="text-lg font-semibold text-center mb-4"></h3>

       {{-- PROFILE FORM --}}
<div id="profileForm" class="hidden" x-data="passwordValidator()">
    <form method="POST" action="{{ route('users.updateProfile') }}">
        @csrf
        @method('PUT')

        @php
    $user = auth()->user();
    $isSuperAdmin = $user && optional($user->role)->name === 'Super Admin';
@endphp

        {{-- NAME --}}
<div class="mb-3">
    <label class="block text-sm font-medium text-gray-700">Name</label>
    <input
        type="text"
        name="name"
        value="{{ optional(auth()->user())->name }}"
        class="mt-1 block w-full rounded shadow-sm
            {{ $isSuperAdmin
                ? 'border-gray-300'
                : 'bg-gray-100 border-gray-300 text-gray-500 cursor-not-allowed'
            }}"
        {{ $isSuperAdmin ? '' : 'readonly' }}
        required
    >
</div>

       {{-- EMAIL --}}
<div class="mb-3">
    <label class="block text-sm font-medium text-gray-700">Email</label>
    <input
        type="email"
        name="email"
        value="{{ optional(auth()->user())->email }}"
        class="mt-1 block w-full rounded shadow-sm
            {{ $isSuperAdmin
                ? 'border-gray-300'
                : 'bg-gray-100 border-gray-300 text-gray-500 cursor-not-allowed'
            }}"
        {{ $isSuperAdmin ? '' : 'readonly' }}
        required
    >
</div>

        {{-- PASSWORD --}}
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input
                type="password"
                name="password"
                x-model="password"
                @input="check()"
                class="mt-1 block w-full border-gray-300 rounded shadow-sm"
                placeholder="Leave blank to keep current password"
            >
        </div>

        {{-- CONFIRM PASSWORD --}}
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input
                type="password"
                name="password_confirmation"
                x-model="confirm"
                class="mt-1 block w-full border-gray-300 rounded shadow-sm"
            >
        </div>

        {{-- PASSWORD REQUIREMENTS --}}
        <div class="mb-4 text-sm">
            <p class="font-medium text-gray-600 mb-1">Password must contain:</p>
            <ul class="space-y-1">
                <li :class="valid.length ? 'text-green-600' : 'text-gray-400'">
                    • At least 6 characters
                </li>
                <li :class="valid.upper ? 'text-green-600' : 'text-gray-400'">
                    • One uppercase letter
                </li>
                <li :class="valid.lower ? 'text-green-600' : 'text-gray-400'">
                    • One lowercase letter
                </li>
                <li :class="valid.special ? 'text-green-600' : 'text-gray-400'">
                    • One special character
                </li>
            </ul>
        </div>

        {{-- SAVE BUTTON --}}
        <div class="flex justify-end mt-4">
            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
                Save Changes
            </button>
        </div>
    </form>
</div>

{{-- ALPINE SCRIPT --}}
<script>
function passwordValidator() {
    return {
        password: '',
        confirm: '',
        valid: {
            length: false,
            upper: false,
            lower: false,
            special: false,
        },
        check() {
            const p = this.password;
            this.valid.length = p.length >= 6;
            this.valid.upper = /[A-Z]/.test(p);
            this.valid.lower = /[a-z]/.test(p);
            this.valid.special = /[^A-Za-z0-9]/.test(p);
        }
    }
}
</script>



        {{-- SWITCH ACCOUNT FORM --}}
        <div id="switchAccountForm" class="hidden">
    <form method="POST" action="{{ route('switch.account') }}">
        @csrf

        {{-- Show validation errors --}}
        @if ($errors->any())
            <div class="mb-3 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email"
                   class="mt-1 block w-full border-gray-300 rounded shadow-sm"
                   required>
        </div>

        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password"
                   class="mt-1 block w-full border-gray-300 rounded shadow-sm"
                   required>
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Login
            </button>
        </div>
    </form>
</div>


    </div>
</div>

<script>
function toggleSettingsDropdown() {
    document.getElementById('settingsDropdown').classList.toggle('hidden');
}

function openProfileModal() {
    closeDropdown();
    openModal('profile');
}

function openSwitchAccountModal() {
    closeDropdown();
    openModal('switch');
}

function openModal(type) {
    const modal = document.getElementById('settingsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('profileForm').classList.add('hidden');
    document.getElementById('switchAccountForm').classList.add('hidden');

    if (type === 'profile') {
        document.getElementById('settingsTitle').innerText = 'Update Profile';
        document.getElementById('profileForm').classList.remove('hidden');
    } else {
        document.getElementById('settingsTitle').innerText = 'Switch Account';
        document.getElementById('switchAccountForm').classList.remove('hidden');
    }
}

function closeSettingsModal() {
    const modal = document.getElementById('settingsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function closeDropdown() {
    document.getElementById('settingsDropdown').classList.add('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('settingsDropdown');
    if (!e.target.closest('.relative')) {
        dropdown.classList.add('hidden');
    }
});

// Close modal after successful POST redirect
@if(session()->has('success'))
    <script>
        document.getElementById('settingsModal').classList.add('hidden');
        document.getElementById('settingsModal').classList.remove('flex');
    </script>
@endif

</script>




        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-100">
            @yield('content')
        </main>

        {{-- FOOTER --}}
<footer class="bg-white shadow p-4 text-center text-gray-600">
    © {{ date('Y') }} eSRRS - Electronic Sports Records and Results System. All rights reserved. <br>
    <!-- Developed by Bryan Lloyd T. Tan -->
</footer>


    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.querySelector('[x-data]');

        document.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');

                // Ignore anchors, javascript, new tabs
                if (
                    !href ||
                    href.startsWith('#') ||
                    href.startsWith('javascript') ||
                    link.target === '_blank'
                ) return;

                // Show loader
                document.querySelector('[x-data]').__x.$data.loading = true;
            });
        });
    });
</script>


</body>
</html>
