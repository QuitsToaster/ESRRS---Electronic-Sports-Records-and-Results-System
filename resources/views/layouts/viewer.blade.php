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

<!-- {{-- FULL PAGE LOADER --}}
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
</script> -->

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
                    <span class="text-lg sm:text-xl font-bold text-gray-800">ESRRS</span>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-6 font-medium text-gray-800 absolute left-1/2 -translate-x-1/2">
                @php $linkClass = 'hover:text-gray-600 transition whitespace-nowrap'; $active = 'underline font-semibold'; @endphp
                <a href="{{ route('viewer.home') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.home') ? $active : '' }}">Home</a>
                <a href="{{ route('viewer.participants') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.participants') ? $active : '' }}">SDO</a>
                <a href="{{ route('viewer.medalTally') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.medalTally') ? $active : '' }}">Medals</a>

                <a href="{{ route('viewer.events') }}" 
   class="px-3 py-2 {{ request()->routeIs('viewer.events') ? 'underline font-semibold' : 'hover:text-gray-600' }}">
    Events
</a>

                <!-- <a href="{{ route('viewer.gallery') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.gallery') ? $active : '' }}">Gallery</a> -->
                <a href="{{ route('viewer.announcements') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.announcements') ? $active : '' }}">Announcements</a>
                <a href="{{ route('viewer.feedbacks') }}" class="{{ $linkClass }} {{ request()->routeIs('viewer.feedbacks') ? $active : '' }}">About Us</a>
            </div>

           {{-- Desktop Actions (Viewer – always visible) --}}
<div class="hidden md:flex items-center gap-3">
    <a href="{{ route('login') }}"
       class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 rounded text-white font-semibold transition">
        Login
    </a>

    <!-- <a href="{{ route('attendance.check') }}"
       class="px-4 py-1.5 bg-yellow-600 hover:bg-yellow-700 rounded text-white font-semibold transition">
        Attendance
    </a> -->
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

        <a href="{{ route('viewer.events') }}" 
   class="block px-3 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('viewer.events') ? 'font-semibold underline' : '' }}">
    Events
</a>

        <!-- <a href="{{ route('viewer.gallery') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Gallery</a> -->
        <a href="{{ route('viewer.announcements') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Announcements</a>
        <a href="{{ route('viewer.feedbacks') }}" class="block px-3 py-2 rounded hover:bg-gray-200">Feedbacks</a>
       {{-- Viewer Actions (Mobile – always visible) --}}
<a href="{{ route('login') }}"
   class="block px-3 py-2 mt-2 rounded bg-blue-600 text-white font-semibold">
    Login
</a>

<!-- <a href="{{ route('attendance.check') }}"
   class="block px-3 py-2 mt-2 rounded bg-yellow-600 text-white font-semibold">
    Attendance
</a> -->


    </div>
</nav>

{{-- Navbar Spacer --}}
<div class="h-16"></div>

{{-- Main Content --}}
<main class="relative z-0 pointer-events-auto w-full overflow-x-hidden">
    @yield('content')
</main>

{{-- ================= FLOATING ANNOUNCEMENTS CRAWLER ================= --}}
@php
    $announcements = \App\Models\Announcement::latest()->take(15)->get();
@endphp

@if($announcements->count())
<div x-data="{
        time: new Date(),
        modalOpen: false,
        modalContent: null,
        showScrollTop: false,
        openModal(content) { this.modalContent = content; this.modalOpen = true },
        closeModal() { this.modalOpen = false; this.modalContent = null },
        handleScroll() { 
            this.showScrollTop = window.scrollY + window.innerHeight >= document.body.scrollHeight * 0.8;
        }
    }"
     x-init="setInterval(() => time = new Date(), 1000); window.addEventListener('scroll', handleScroll)"
     class="fixed bottom-0 left-0 right-0 z-[70] font-[Poppins]">

    {{-- DESKTOP VIEW --}}
    <div class="hidden sm:flex w-full items-center shadow-lg overflow-hidden">
        {{-- LIVE + Time Container --}}
        <div class="flex-shrink-0 flex flex-col">
            {{-- LIVE/NEWS Badge --}}
            <div class="px-2 py-2 bg-red-700 text-white font-bold  text-xl text-center">
                eSRRS NEWS
            </div>

            {{-- Time Box --}}
            <div class="px-2 py-2 bg-yellow-500 text-black text-sm font-bold flex items-center gap-2">
                <div x-text="time.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })"></div>
                <div>|</div>
                <div x-text="time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })"></div>
            </div>
        </div>

        {{-- Marquee --}}
        <div class="overflow-hidden flex-1 relative bg-white pb-6">
            <div class="flex gap-8 animate-marquee p-2 items-center">
                @foreach([$announcements, $announcements] as $announcementSet)
                    @foreach($announcementSet as $announcement)
                        <span @click="openModal({{ json_encode($announcement) }})"
                              class="cursor-pointer text-gray-800 text-sm sm:text-base flex items-center gap-1">
                            {{ $announcement->event_name ?? $announcement->title ?? 'Announcement' }}
                            <span class="mx-2 font-semibold">|</span>
                        </span>
                    @endforeach
                @endforeach
            </div>

            {{-- Secondary info bar --}}
            <div class="absolute bottom-0 left-0 w-full bg-blue-900 text-white text-xs px-2 py-1">
                Latest announcements • Click for details
            </div>
        </div>
    </div>

    {{-- MOBILE VIEW --}}
    <div class="flex sm:hidden w-full overflow-hidden bg-white shadow-lg">
        <div class="flex-1 overflow-hidden relative">
            <div class="flex gap-6 animate-marquee-mobile p-2 items-center">
                @foreach([$announcements, $announcements] as $announcementSet)
                    @foreach($announcementSet as $announcement)
                        <span @click="openModal({{ json_encode($announcement) }})"
                              class="cursor-pointer text-gray-900 text-base font-semibold flex items-center gap-1">
                            {{ $announcement->event_name ?? $announcement->title ?? 'Announcement' }}
                            <span class="mx-2 font-bold">|</span>
                        </span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- Scroll To Top Button (appears when near bottom) --}}
    <button x-show="showScrollTop"
            x-transition
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-16 right-4 z-[80] px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-semibold shadow-lg">
        ↑ Top
    </button>

    {{-- Modal (TV-like design) --}}
    <div x-show="modalOpen" x-transition.opacity
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60">
        <div @click.away="closeModal()"
             class="bg-gray-900 rounded-xl shadow-2xl max-w-lg w-full border-4 border-red-700 overflow-hidden">
            {{-- Header --}}
            <div class="bg-red-700 px-4 py-3">
                <h2 class="text-white text-lg font-bold" x-text="modalContent ? (modalContent.event_name ?? modalContent.title) : ''"></h2>
            </div>

            {{-- Body --}}
            <div class="bg-gray-100 px-4 py-4 text-gray-800 text-sm sm:text-base">
                <p x-text="modalContent ? modalContent.content : ''"></p>
            </div>

            {{-- Footer / Type --}}
            <div class="bg-gray-800 px-4 py-2 text-white text-xs flex justify-between items-center">
                <span x-text="modalContent ? modalContent.type : ''"></span>
                <span class="italic">eSRRS • Click anywhere to close</span>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ================= MARQUEE ANIMATION ================= --}}
<style>
@keyframes marquee {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
}
@keyframes marquee-mobile {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-100%); }
}
.animate-marquee {
    display: inline-flex;
    gap: 2rem;
    white-space: nowrap;
    animation: marquee 60s linear infinite;
}
.animate-marquee:hover {
    animation-play-state: paused;
}
.animate-marquee-mobile {
    display: inline-flex;
    gap: 1.5rem;
    white-space: nowrap;
    font-size: 1.1rem;
    animation: marquee-mobile 90s linear infinite;
}
.animate-marquee-mobile:hover {
    animation-play-state: paused;
}
</style>


{{-- ================= FOOTER ================= --}}
@php $footer = \App\Models\FooterSetting::first(); @endphp
<footer class="bg-black text-white mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Logo & Name --}}
        <div class="flex flex-col items-start gap-2">
            @if($footer && $footer->logo)
                <img src="{{ asset('storage/' . $footer->logo) }}" alt="Footer Logo" class="h-16 object-contain">
            @endif
            <span class="text-2xl font-bold tracking-wide">{{ $footer->logo_name ?? 'ESRRS' }}</span>
        </div>

        {{-- Quick Links --}}
        <div class="flex flex-col gap-2">
            <h3 class="text-2xl font-semibold mb-2" style="font-family: 'Poppins', sans-serif;">Quick Links</h3>
            <a href="{{ route('viewer.home') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Home</a>
            <a href="{{ route('viewer.participants') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">SDO</a>
            <a href="{{ route('viewer.medalTally') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Medals</a>
            <a href="{{ route('viewer.events') }}" 
            class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">
                Events
            </a>
            <a href="{{ route('viewer.gallery') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Gallery</a>
            <a href="{{ route('viewer.announcements') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">Announcements</a>
            <a href="{{ route('viewer.feedbacks') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">About Us</a>
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

    {{-- =======================
         Hardcoded Horizontal 4 Images
         Replace 'URL_HERE' with your 4 image paths
    ======================== --}}
    <div class="max-w-7xl mx-auto px-6 py-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
        <img src="{{ asset('storage/images/footer/image1.png') }}" alt="Footer Image 1" class="w-full h-20 object-contain rounded">
<img src="{{ asset('storage/images/footer/image2.png') }}" alt="Footer Image 2" class="w-full h-20 object-contain rounded">
<img src="{{ asset('storage/images/footer/image3.png') }}" alt="Footer Image 3" class="w-full h-20 object-contain rounded">
<img src="{{ asset('storage/images/footer/image4.png') }}" alt="Footer Image 4" class="w-full h-20 object-contain rounded">


        {{-- Replace the src with your actual image paths --}}
    </div>

    {{-- Footer Bottom Bar --}}
    <div class="border-t border-gray-800 mt-8 py-4 text-center text-gray-500 text-sm">
        Developed by Bryan Lloyd T. Tan &copy; {{ date('Y') }}
    </div>
</footer>


</body>
</html>
