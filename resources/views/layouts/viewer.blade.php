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
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
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

{{-- ================= TV NEWS STYLE ANNOUNCEMENTS CRAWLER ================= --}}
@php
    $announcements = \App\Models\Announcement::latest()->take(20)->get();
@endphp

@if($announcements->count())
<div x-data="{
        time: new Date(),
        modalOpen: false,
        modalContent: null,
        showScrollTop: false,
        openModal(content) { 
            this.modalContent = content; 
            this.modalOpen = true;
            // Pause crawler when modal is open
            document.querySelector('.crawler-content')?.style.setProperty('animation-play-state', 'paused');
        },
        closeModal() { 
            this.modalOpen = false; 
            this.modalContent = null;
            // Resume crawler
            document.querySelector('.crawler-content')?.style.setProperty('animation-play-state', 'running');
        },
        handleScroll() { 
            this.showScrollTop = window.scrollY + window.innerHeight >= document.body.scrollHeight * 0.8;
        }
    }"
     x-init="
        setInterval(() => time = new Date(), 1000); 
        window.addEventListener('scroll', handleScroll);
        // Add keyboard support for modal
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalOpen) closeModal();
        });
     "
     class="fixed bottom-0 left-0 right-0 z-[70] font-['Inter',system-ui,sans-serif]">

    {{-- DESKTOP TV NEWS STYLE --}}
    <div class="hidden md:block w-full bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 border-t-2 border-red-600 shadow-2xl">
        <div class="flex items-stretch h-20">
            {{-- LIVE TV BADGE WITH GLOW EFFECT --}}
            <div class="relative flex-shrink-0 w-40 bg-gradient-to-br from-red-700 to-red-800 flex items-center justify-center overflow-hidden">
                <div class="absolute inset-0 bg-white/10 skew-x-12 -translate-x-20 group-hover:translate-x-40 transition-transform duration-1000"></div>
                <div class="text-center relative">
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <span class="text-white font-black text-xl tracking-wider">LIVE</span>
                    </div>
                    <span class="text-white/80 text-[10px] font-mono tracking-widest">eSRRS NEWS</span>
                </div>
            </div>

            {{-- TIME/DATE TICKER --}}
            <div class="flex-shrink-0 w-48 bg-gradient-to-b from-gray-800 to-gray-900 border-r border-l border-gray-700 flex items-center justify-center px-4">
                <div class="text-center">
                    <div class="text-amber-400 font-bold text-sm flex items-center justify-center gap-2">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span x-text="time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true })"></span>
                    </div>
                    <div class="text-gray-300 text-xs font-medium" x-text="time.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }).toUpperCase()"></div>
                </div>
            </div>

            {{-- NEWS TICKER/CRAWLER --}}
            <div class="flex-1 overflow-hidden relative bg-gray-800/50 backdrop-blur-sm">
                {{-- Gradient overlays for fade effect --}}
                <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-gray-800 to-transparent z-10"></div>
                <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-gray-800 to-transparent z-10"></div>

                {{-- Marquee Content --}}
                <div class="crawler-content flex gap-12 pl-48 py-6 animate-crawler items-center whitespace-nowrap">
                    @foreach($announcements as $announcement)
                        <div @click="openModal({{ json_encode($announcement) }})" 
                             class="inline-flex items-center gap-4 cursor-pointer group hover:scale-105 transition-transform duration-200">
                            {{-- Category Badge --}}
                            <span class="px-2 py-1 text-xs font-bold rounded 
                                @if($announcement->type === 'Urgent') bg-red-600 text-white
                                @elseif($announcement->type === 'Update') bg-blue-600 text-white
                                @else bg-gray-700 text-gray-200
                                @endif">
                                {{ strtoupper($announcement->type ?? 'NEWS') }}
                            </span>
                            
                            {{-- Title --}}
                            <span class="text-white font-medium group-hover:text-amber-400 transition-colors">
                                {{ $announcement->event_name ?? $announcement->title }}
                            </span>
                            
                            {{-- Separator --}}
                            <span class="text-gray-600 text-lg font-bold">●</span>
                        </div>
                    @endforeach
                    
                    {{-- Duplicate for seamless loop --}}
                    @foreach($announcements as $announcement)
                        <div @click="openModal({{ json_encode($announcement) }})" 
                             class="inline-flex items-center gap-4 cursor-pointer group hover:scale-105 transition-transform duration-200">
                            <span class="px-2 py-1 text-xs font-bold rounded 
                                @if($announcement->type === 'Urgent') bg-red-600 text-white
                                @elseif($announcement->type === 'Update') bg-blue-600 text-white
                                @else bg-gray-700 text-gray-200
                                @endif">
                                {{ strtoupper($announcement->type ?? 'NEWS') }}
                            </span>
                            <span class="text-white font-medium group-hover:text-amber-400 transition-colors">
                                {{ $announcement->event_name ?? $announcement->title }}
                            </span>
                            <span class="text-gray-600 text-lg font-bold">●</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Secondary Info Bar --}}
        <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white/80 text-[10px] px-4 py-1 flex items-center justify-between border-t border-blue-700">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-mono">{{ $announcements->count() }} UPDATES</span>
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span>CLICK ANY NEWS FOR DETAILS</span>
                </span>
            </div>
            <span class="font-mono tracking-widest">⚡ LIVE FEED</span>
        </div>
    </div>

    {{-- MOBILE VIEW --}}
    <div class="md:hidden w-full bg-gradient-to-r from-gray-900 to-gray-800 border-t-2 border-red-600 shadow-2xl">
        <div class="flex items-stretch h-16">
            {{-- Mobile LIVE Badge --}}
            <div class="flex-shrink-0 w-24 bg-gradient-to-br from-red-700 to-red-800 flex items-center justify-center">
                <div class="text-center">
                    <span class="text-white font-black text-sm tracking-wider flex items-center gap-1">
                        <span class="animate-pulse h-2 w-2 bg-red-400 rounded-full"></span>
                        LIVE
                    </span>
                </div>
            </div>

            {{-- Mobile Time --}}
            <div class="flex-shrink-0 w-20 bg-gray-800 flex items-center justify-center text-center border-l border-gray-700">
                <div>
                    <div class="text-amber-400 text-xs font-bold" x-text="time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })"></div>
                    <div class="text-gray-400 text-[8px]" x-text="time.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }).toUpperCase()"></div>
                </div>
            </div>

            {{-- Mobile Crawler --}}
            <div class="flex-1 overflow-hidden relative bg-gray-800/50">
                <div class="crawler-content-mobile flex gap-6 pl-24 py-5 animate-crawler-mobile items-center whitespace-nowrap">
                    @foreach($announcements as $announcement)
                        <div @click="openModal({{ json_encode($announcement) }})" 
                             class="inline-flex items-center gap-2 cursor-pointer">
                            <span class="text-white text-xs font-medium">{{ $announcement->event_name ?? $announcement->title }}</span>
                            <span class="text-gray-600 text-sm font-bold">●</span>
                        </div>
                    @endforeach
                    @foreach($announcements as $announcement)
                        <div @click="openModal({{ json_encode($announcement) }})" 
                             class="inline-flex items-center gap-2 cursor-pointer">
                            <span class="text-white text-xs font-medium">{{ $announcement->event_name ?? $announcement->title }}</span>
                            <span class="text-gray-600 text-sm font-bold">●</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- TV STYLE MODAL --}}
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         @click="closeModal()">
        
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-700 overflow-hidden transform transition-all"
             @click.stop>
            
            {{-- TV Modal Header --}}
            <div class="bg-gradient-to-r from-red-700 to-red-800 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                    </div>
                    <h2 class="text-white font-bold text-xl tracking-tight" 
                        x-text="modalContent ? (modalContent.event_name ?? modalContent.title) : ''"></h2>
                </div>
                <button @click="closeModal()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- TV Modal Content --}}
            <div class="px-6 py-6 bg-gray-800">
                {{-- Type Badge --}}
                <div class="inline-block px-3 py-1 mb-4 rounded-full text-xs font-bold tracking-wider"
                     :class="{
                         'bg-red-600 text-white': modalContent?.type === 'Urgent',
                         'bg-blue-600 text-white': modalContent?.type === 'Update',
                         'bg-gray-700 text-gray-200': !['Urgent', 'Update'].includes(modalContent?.type)
                     }"
                     x-text="modalContent ? (modalContent.type || 'ANNOUNCEMENT') : ''">
                </div>

                {{-- Content --}}
                <div class="prose prose-invert max-w-none">
                    <p class="text-gray-200 text-base leading-relaxed" x-text="modalContent ? modalContent.content : ''"></p>
                </div>

                {{-- Timestamp --}}
                <div class="mt-6 pt-4 border-t border-gray-700 flex items-center justify-between text-xs text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="modalContent ? new Date(modalContent.created_at).toLocaleString() : ''"></span>
                    </span>
                    <span class="font-mono">eSRRS NEWS NETWORK</span>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="bg-gray-900 px-6 py-3 text-xs text-gray-500 flex justify-between items-center border-t border-gray-700">
                <span>PRESS ESC TO CLOSE</span>
                <span class="italic">CLICK OUTSIDE TO CONTINUE</span>
            </div>
        </div>
    </div>

    {{-- Scroll to Top Button --}}
    <button x-show="showScrollTop"
            x-transition
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-24 right-4 z-[80] px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg text-white font-semibold shadow-xl flex items-center gap-2 border border-blue-400/30 backdrop-blur-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
        TOP
    </button>
</div>

{{-- ================= CRAWLER ANIMATIONS ================= --}}
<style>
@keyframes crawl {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
}
@keyframes crawl-mobile {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-100%); }
}
.crawler-content {
    display: inline-flex;
    gap: 3rem;
    white-space: nowrap;
    animation: crawl 50s linear infinite;
    will-change: transform;
}
.crawler-content:hover {
    animation-play-state: paused;
}
.crawler-content-mobile {
    display: inline-flex;
    gap: 2rem;
    white-space: nowrap;
    animation: crawl-mobile 180s linear infinite;
    will-change: transform;
}
.crawler-content-mobile:hover {
    animation-play-state: paused;
}

/* TV static effect on hover */
.crawler-content:hover ~ .gradient-overlay {
    opacity: 0.5;
}

/* Smooth transitions */
.modal-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar for modal */
.modal-content::-webkit-scrollbar {
    width: 6px;
}
.modal-content::-webkit-scrollbar-track {
    background: #374151;
}
.modal-content::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}
.modal-content::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>
@endif


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

    <!-- {{-- Footer Bottom Bar --}}
    <div class="border-t border-gray-800 mt-8 py-4 text-center text-gray-500 text-sm">
        Developed by Bryan Lloyd T. Tan &copy; {{ date('Y') }}
    </div> -->
</footer>


</body>
</html>
