<!DOCTYPE html>
<html lang="en"
      x-data="{
        open: false,
        showTop: false
      }"
      x-init="
        window.addEventListener('scroll', () => {
            showTop = window.scrollY > 300
        })
      ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Viewer Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100 font-sans">

    {{-- Fixed Top Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-blue-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                {{-- Logo --}}
                @php
                    $viewerLogo = \App\Models\LayoutSetting::where('key', 'viewer_top_logo')->first();
                @endphp

                <a href="{{ route('viewer.home') }}" class="flex items-center gap-2">
                    @if($viewerLogo && $viewerLogo->value)
                        <img src="{{ asset('storage/' . $viewerLogo->value) }}"
                             alt="Viewer Logo"
                             class="h-10 object-contain">
                    @else
                        <span class="text-lg font-bold">CEMS</span>
                    @endif
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center space-x-6 font-medium">
                    @php
                        $linkClass = 'hover:text-gray-200';
                        $active = 'underline font-semibold';
                    @endphp

                    <a href="{{ route('viewer.home') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.home') ? $active : '' }}">Home</a>
                    <a href="{{ route('viewer.participants') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.participants') ? $active : '' }}">Teams</a>
                    <a href="{{ route('viewer.medalTally') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.medalTally') ? $active : '' }}">Medal Tally</a>
                    {{-- Combined Competitions Nav --}}
                    <div x-data="{ openComp: false }" class="relative">
                        <button @click="openComp = !openComp"
                                class="flex items-center gap-1 {{ request()->routeIs('viewer.events','viewer.games') ? $active : '' }}">
                            Competitions
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openComp"
                             x-transition
                             @click.away="openComp = false"
                             class="absolute mt-2 bg-white text-gray-800 rounded shadow-lg w-36 z-50">
                            <a href="{{ route('viewer.events') }}" class="block px-4 py-2 hover:bg-gray-100">Events</a>
                            <a href="{{ route('viewer.games') }}" class="block px-4 py-2 hover:bg-gray-100">Games</a>
                        </div>
                    </div>

                    <a href="{{ route('viewer.gallery') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.gallery') ? $active : '' }}">Gallery</a>
                    <a href="{{ route('viewer.announcements') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.announcements') ? $active : '' }}">Announcements</a>
                    <a href="{{ route('viewer.feedbacks') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.feedbacks') ? $active : '' }}">Feedbacks</a>

                    <a href="{{ url('/') }}"
                       class="ml-4 px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">
                        Logout
                    </a>
                </div>

                {{-- Mobile Menu Button --}}
                <button @click="open = !open" class="md:hidden focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open"
             x-transition
             x-cloak
             class="md:hidden bg-blue-700 px-2 pt-2 pb-3 space-y-1">

            <a href="{{ route('viewer.home') }}" class="block px-3 py-2 rounded hover:bg-blue-600">Home</a>
            <a href="{{ route('viewer.participants') }}" class="block px-3 py-2 rounded hover:bg-blue-600">Teams</a>
            <a href="{{ route('viewer.medalTally') }}" class="block px-3 py-2 rounded hover:bg-blue-600">Medal Tally</a>
            {{-- Combined Competitions --}}
            <div x-data="{ openComp: false }" class="relative">
                <button @click="openComp = !openComp" class="w-full text-left px-3 py-2 rounded hover:bg-blue-600 flex justify-between items-center">
                    Competitions
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openComp" x-transition @click.away="openComp = false" class="bg-blue-600 mt-1 rounded shadow-lg">
                    <a href="{{ route('viewer.events') }}" class="block px-4 py-2 hover:bg-blue-500">Events</a>
                    <a href="{{ route('viewer.games') }}" class="block px-4 py-2 hover:bg-blue-500">Games</a>
                </div>
            </div>

            <a href="{{ route('viewer.gallery') }}" class="block px-3 py-2 rounded hover:bg-blue-600">Gallery</a>
            <a href="{{ route('viewer.announcements') }}" class="block px-3 py-2 rounded hover:bg-blue-600">Announcements</a>
            <a href="{{ route('viewer.feedbacks') }}" class="block px-3 py-2 rounded hover:bg-blue-600">Feedbacks</a>

            <a href="{{ url('/') }}"
               class="block px-3 py-2 mt-1 rounded bg-red-600 hover:bg-red-700 font-semibold">
                Logout
            </a>
        </div>
    </nav>

    {{-- Offset for fixed navbar --}}
    <div class="h-16"></div>

    {{-- Main Content FULL WIDTH --}}
    <main class="w-full">
        @yield('content')
    </main>

    {{-- Scroll to Top Button --}}
    <button
        x-show="showTop"
        x-transition
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-50 bg-blue-600 hover:bg-blue-700
               text-white w-12 h-12 rounded-full shadow-lg
               flex items-center justify-center"
        aria-label="Scroll to top"
    >
        ↑
    </button>

</body>
</html>
