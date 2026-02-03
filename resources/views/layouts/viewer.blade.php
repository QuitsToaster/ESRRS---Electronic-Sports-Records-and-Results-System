<!DOCTYPE html>
<html lang="en"
      x-data="{ open: false, showTop: false }"
      x-init="
        window.addEventListener('scroll', () => { showTop = window.scrollY > 300 });
        window.addEventListener('resize', () => { if (window.innerWidth >= 768) open = false });
      "
      class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>@yield('page-title', 'Viewer Dashboard')</title>

    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans overflow-x-hidden">

{{-- FULL PAGE LOADER --}}
<div
    x-data="{ loading: true }"
    x-init="
        window.addEventListener('load', () => {
            setTimeout(() => loading = false, 500);
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

    {{-- Dynamic Motivational Loading Text --}}
    <p class="text-sm font-medium text-gray-600 tracking-wide">
        <span id="loadingText">Gearing up for the action…</span>
    </p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const phrases = [
            "Gearing up for the action…",
            "Counting medals… 🏅",
            "Warming up the teams…",
            "Getting ready for the games…",
            "CAVRAA Viewer loading… 💪",
            "Checking the scoreboard…",
            "Fueling the competitive spirit… 🏆"
        ];

        let index = 0;
        const loadingText = document.getElementById('loadingText');

        setInterval(() => {
            index = (index + 1) % phrases.length;
            loadingText.textContent = phrases[index];
        }, 1500); // change every 1.5 seconds
    });
</script>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loader = document.querySelector('[x-data="{ loading: true }"]') || document.querySelector('[x-data]');

    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');

            // Ignore anchors, JS links, or links that open in a new tab
            if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank') return;

            // Show loader
            if (loader && loader.__x) {
                loader.__x.$data.loading = true;
            }
        });
    });
});
</script>


{{-- ================= NAVBAR ================= --}}
<nav class="fixed top-0 inset-x-0 z-[60] pointer-events-auto bg-white/30 backdrop-blur-md shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 relative">
            {{-- Logo --}}
            @php $viewerLogo = \App\Models\LayoutSetting::where('key', 'viewer_top_logo')->first(); @endphp
            <a href="{{ route('viewer.home') }}" class="flex items-center gap-2 shrink-0">
                @if($viewerLogo && $viewerLogo->value)
                    <img src="{{ asset('storage/' . $viewerLogo->value) }}" alt="Viewer Logo" class="h-8 sm:h-10 object-contain">
                @else
                    <span class="text-lg sm:text-xl font-bold text-gray-800">CEMS</span>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-6 font-medium text-gray-800 absolute left-1/2 -translate-x-1/2">
                @php $linkClass = 'hover:text-gray-600 transition whitespace-nowrap'; $active = 'underline font-semibold'; @endphp
                <a href="{{ route('viewer.home') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.home') ? $active : '' }}">Home</a>
                <a href="{{ route('viewer.participants') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.participants') ? $active : '' }}">Teams</a>
                <a href="{{ route('viewer.medalTally') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.medalTally') ? $active : '' }}">Medals</a>

                <div x-data="{ openComp: false }" class="relative">
                    <button @click="openComp = !openComp" class="flex items-center gap-1 {{ request()->routeIs('viewer.events','viewer.games') ? $active : '' }}">
                        Competitions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openComp" x-transition @click.away="openComp = false" class="absolute mt-2 w-36 bg-white rounded shadow-lg text-gray-800 z-[70]">
                        <a href="{{ route('viewer.events') }}" class="block px-4 py-2 hover:bg-gray-100">Events</a>
                        <a href="{{ route('viewer.games') }}" class="block px-4 py-2 hover:bg-gray-100">Games</a>
                    </div>
                </div>

                <a href="{{ route('viewer.gallery') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.gallery') ? $active : '' }}">Gallery</a>
                <a href="{{ route('viewer.announcements') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.announcements') ? $active : '' }}">Announcements</a>
                <a href="{{ route('viewer.feedbacks') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.feedbacks') ? $active : '' }}">Feedbacks</a>
            </div>

           {{-- Desktop Actions (Viewer – always visible) --}}
<div class="hidden md:flex items-center gap-3">
    <a href="{{ route('login') }}"
       class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 rounded text-white font-semibold transition">
        Login
    </a>

    <a href="{{ route('attendance.check') }}"
       class="px-4 py-1.5 bg-yellow-600 hover:bg-yellow-700 rounded text-white font-semibold transition">
        Attendance
    </a>
</div>


            {{-- Mobile Toggle --}}
            <button @click="open = !open" class="md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-transition x-cloak class="md:hidden bg-white/90 backdrop-blur-md px-3 py-3 space-y-1 z-[65]">
        <a href="{{ route('viewer.home') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Home</a>
        <a href="{{ route('viewer.participants') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Teams</a>
        <a href="{{ route('viewer.medalTally') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Medals</a>

        <div x-data="{ openComp: false }">
            <button @click="openComp = !openComp" class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-200">
                Competitions
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="openComp" x-transition class="pl-4">
                <a href="{{ route('viewer.events') }}" class="block px-3 py-2 hover:bg-gray-100 rounded">Events</a>
                <a href="{{ route('viewer.games') }}" class="block px-3 py-2 hover:bg-gray-100 rounded">Games</a>
            </div>
        </div>

        <a href="{{ route('viewer.gallery') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Gallery</a>
        <a href="{{ route('viewer.announcements') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Announcements</a>
        <a href="{{ route('viewer.feedbacks') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Feedbacks</a>
       {{-- Viewer Actions (Mobile – always visible) --}}
<a href="{{ route('login') }}"
   class="block px-3 py-2 mt-2 rounded bg-blue-600 text-white font-semibold">
    Login
</a>

<a href="{{ route('attendance.check') }}"
   class="block px-3 py-2 mt-2 rounded bg-yellow-600 text-white font-semibold">
    Attendance
</a>


    </div>
</nav>

{{-- Navbar Spacer --}}
<div class="h-16"></div>

{{-- Main Content --}}
<main class="relative z-0 pointer-events-auto w-full overflow-x-hidden">
    @yield('content')
</main>

{{-- Scroll To Top --}}
<button x-show="showTop"
        x-transition
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-4 sm:right-6 z-[60] bg-blue-600 hover:bg-blue-700 text-white w-11 h-11 sm:w-12 sm:h-12 rounded-full shadow-lg flex items-center justify-center">
    ↑
</button>

{{-- ================= FOOTER ================= --}}
@php $footer = \App\Models\FooterSetting::first(); @endphp
<footer class="bg-black text-white mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Logo & Name --}}
        <div class="flex flex-col items-start gap-2">
            @if($footer && $footer->logo)
                <img src="{{ asset('storage/' . $footer->logo) }}" alt="Footer Logo" class="h-16 object-contain">
            @endif
            <span class="text-2xl font-bold tracking-wide">{{ $footer->logo_name ?? 'CEMS' }}</span>
        </div>

        {{-- Quick Links --}}
        <div class="flex flex-col gap-2">
            <h3 class="text-2xl font-semibold mb-2" style="font-family: 'Poppins', sans-serif;">Quick Links</h3>
            <a href="{{ route('viewer.home') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Home</a>
            <a href="{{ route('viewer.participants') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Teams</a>
            <a href="{{ route('viewer.medalTally') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Medals</a>
            <div x-data="{ openComp: false }" class="relative" style="font-family: 'Poppins', sans-serif;">
                    <button @click="openComp = !openComp" class="flex items-center gap-1 {{ request()->routeIs('viewer.events','viewer.games') ? $active : '' }}">
                        Competitions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openComp" x-transition @click.away="openComp = false" class="absolute mt-2 w-36 bg-white rounded shadow-lg text-gray-800 z-[70]">
                        <a href="{{ route('viewer.events') }}" class="block px-4 py-2 hover:bg-gray-100" style="font-family: 'Poppins', sans-serif;">Events</a>
                        <a href="{{ route('viewer.games') }}" class="block px-4 py-2 hover:bg-gray-100" style="font-family: 'Poppins', sans-serif;">Games</a>
                    </div>
                </div>
            <a href="{{ route('viewer.gallery') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Gallery</a>
            <a href="{{ route('viewer.announcements') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif; ">Announcements</a>
            <a href="{{ route('viewer.feedbacks') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Feedbacks</a>
        </div>

        {{-- Social Media --}}
        <div class="flex flex-col gap-2">
            <h3 class="text-2xl font-semibold mb-2" style="font-family: 'Poppins', sans-serif;">Follow Us</h3>
            @if($footer && $footer->socials)
                @foreach($footer->socials as $social)
                    <a href="{{ $social['link'] }}" target="_blank" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">
                        {{ $social['name'] }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Footer Bottom Bar --}}
    <div class="border-t border-gray-800 mt-8 py-4 text-center text-gray-500 text-sm">
        Developed by Bryan Lloyd T. Tan &copy; {{ date('Y') }}
    </div>
</footer>

</body>
</html>
