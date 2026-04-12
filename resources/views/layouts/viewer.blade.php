<!DOCTYPE html>
<html lang="en"
      x-data="viewerLayout()"
      x-init="initLayout()"
      class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <title>@yield('page-title', 'Viewer Dashboard')</title>

    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/html5-qrcode" defer></script>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 font-['Poppins',sans-serif] overflow-x-hidden">

{{-- ================= CLEAN WHITE NAVBAR ================= --}}
<nav class="fixed top-0 inset-x-0 z-[60] pointer-events-auto bg-white/95 backdrop-blur-md shadow-md border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 relative">
            {{-- Logo --}}
            @php $viewerLogo = \App\Models\LayoutSetting::where('key', 'viewer_top_logo')->first(); @endphp
            <a href="{{ route('viewer.home') }}" class="flex items-center gap-2 shrink-0 group">
                <div class="relative">
                    @if($viewerLogo && $viewerLogo->value)
                        <img src="{{ asset('storage/' . $viewerLogo->value) }}" alt="Viewer Logo" class="relative h-8 sm:h-10 object-contain">
                    @else
                        <span class="relative text-xl sm:text-2xl font-black text-gray-900 tracking-tight">ESRRS</span>
                    @endif
                </div>
            </a>

            {{-- Desktop Navigation - Clean White Style --}}
            <div class="hidden md:flex items-center gap-1 font-medium text-gray-700 absolute left-1/2 -translate-x-1/2">
                @php
                    $linkClass = 'px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200';
                    $activeClass = 'bg-blue-50 text-blue-700 font-semibold';
                @endphp
                
                <a href="{{ route('viewer.home') }}" 
                   class="{{ $linkClass }} {{ request()->routeIs('viewer.home') ? $activeClass : '' }}">
                    Home
                </a>
                
                <a href="{{ route('viewer.participants') }}" 
                   class="{{ $linkClass }} {{ request()->routeIs('viewer.participants') ? $activeClass : '' }}">
                    SDO
                </a>
                
                <a href="{{ route('viewer.medalTally') }}" 
                   class="{{ $linkClass }} {{ request()->routeIs('viewer.medalTally') ? $activeClass : '' }}">
                    Medals
                </a>

                <a href="{{ route('viewer.events') }}" 
                   class="{{ $linkClass }} {{ request()->routeIs('viewer.events') ? $activeClass : '' }}">
                    Events
                </a>

                <a href="{{ route('viewer.announcements') }}" 
                   class="{{ $linkClass }} {{ request()->routeIs('viewer.announcements') ? $activeClass : '' }}">
                    News
                </a>
                
                <a href="{{ route('viewer.feedbacks') }}" 
                   class="{{ $linkClass }} {{ request()->routeIs('viewer.feedbacks') ? $activeClass : '' }}">
                    About
                </a>

                <a href="{{ route('viewer.me') }}" 
                    class="{{ $linkClass }} {{ request()->routeIs('viewer.me') ? $activeClass : '' }}">
                    M&E
                </a>
            </div>

            {{-- Desktop Actions - Clean White Buttons --}}
            <div class="hidden md:flex items-center gap-3">
                <button type="button"
                        @click="openScanner()"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm hover:shadow transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V6a2 2 0 012-2h1m12 0h1a2 2 0 012 2v1m0 10v1a2 2 0 01-2 2h-1m-12 0H5a2 2 0 01-2-2v-1m3-7h12M8 12h.01M12 12h.01M16 12h.01" />
                    </svg>
                    Scan ID
                </button>

                <a href="{{ route('login') }}"
                   class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login
                </a>
            </div>

            {{-- Mobile Toggle --}}
            <button @click="open = !open" class="md:hidden focus:outline-none text-gray-700 p-2">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu - Clean White Style --}}
    <div x-show="open" x-transition x-cloak 
         class="md:hidden bg-white border-t border-gray-200 px-3 py-4 space-y-2 z-[65] shadow-lg">
        
        <a href="{{ route('viewer.home') }}" 
           class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.home') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            Home
        </a>
        
        <a href="{{ route('viewer.participants') }}" 
           class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.participants') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            SDO
        </a>
        
        <a href="{{ route('viewer.medalTally') }}" 
           class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.medalTally') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            Medals
        </a>

        <a href="{{ route('viewer.events') }}" 
           class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.events') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            Events
        </a>

        <a href="{{ route('viewer.announcements') }}" 
           class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.announcements') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            News
        </a>
        
        <a href="{{ route('viewer.feedbacks') }}" 
           class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.feedbacks') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            About
        </a>

        <a href="{{ route('viewer.me') }}" 
            class="block px-4 py-3 rounded-lg text-gray-700 font-medium {{ request()->routeIs('viewer.me') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }} transition-colors">
            M&E
        </a>

        {{-- Mobile Actions --}}
        <div class="pt-4 mt-2 border-t border-gray-200 space-y-2">
            <button type="button"
                    @click="openScanner(); open = false"
                    class="block w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg text-center transition-colors">
                Scan ID
            </button>
            <a href="{{ route('login') }}"
               class="block px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-center transition-colors">
                Login
            </a>
        </div>
    </div>
</nav>

{{-- Navbar Spacer --}}
<div class="h-16"></div>

{{-- ================= PUBLIC QR SCANNER MODAL ================= --}}
<div x-show="scannerOpen"
     x-cloak
     x-transition
     class="fixed inset-0 z-[120] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
     @click.self="closeScanner()">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-green-600 to-green-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V6a2 2 0 012-2h1m12 0h1a2 2 0 012 2v1m0 10v1a2 2 0 01-2 2h-1m-12 0H5a2 2 0 01-2-2v-1m3-7h12" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg">Scan Athlete ID</h2>
                    <p class="text-white/80 text-sm">Scan to see athlete details</p>
                </div>
            </div>

            <button type="button" @click="closeScanner()" class="text-white/90 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-5">
            <div class="bg-gray-100 rounded-xl p-3 border border-gray-200">
                <div id="public-qr-reader" class="w-full overflow-hidden rounded-lg"></div>
            </div>

            <p class="mt-4 text-sm text-gray-600">
                Point the camera at the athlete QR code. Old printed IDs with raw
            </p>

            <template x-if="scannerError">
                <div class="mt-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm" x-text="scannerError"></div>
            </template>

            <template x-if="scannerLoading">
                <div class="mt-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                    Opening camera...
                </div>
            </template>

            <div class="mt-5 flex items-center justify-end gap-3">
                <button type="button"
                        @click="closeScanner()"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<main class="relative z-0 pointer-events-auto w-full overflow-x-hidden">
    @yield('content')
</main>

{{-- Scroll to Top Button (clean version) --}}
<button x-show="showTop" 
        x-cloak
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition
        class="fixed bottom-6 right-6 z-50 p-3 bg-white hover:bg-gray-50 text-gray-700 rounded-full shadow-lg hover:shadow-xl border border-gray-200 transform hover:scale-110 transition-all duration-200">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<style>
    @keyframes marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .animate-marquee {
        display: inline-block;
        animation: marquee 30s linear infinite;
    }
    .animate-marquee:hover {
        animation-play-state: paused;
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
</style>

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
            <a href="{{ route('viewer.announcements') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">News</a>
            <a href="{{ route('viewer.feedbacks') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">About Us</a>
            <a href="{{ route('viewer.me') }}" class="hover:text-blue-600 transition" style="font-family: 'Poppins', sans-serif;">
                M&E
            </a>
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

<script>
function viewerLayout() {
    return {
        open: false,
        showTop: false,

        scannerOpen: false,
        scannerLoading: false,
        scannerError: '',
        qrScannerInstance: null,
        qrScannerStarted: false,

        initLayout() {
    // ================= SCROLL BUTTON =================
    window.addEventListener('scroll', () => {
        this.showTop = window.scrollY > 300;
    });

    // ================= MOBILE MENU AUTO CLOSE =================
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) this.open = false;
    });

    // ================= AUTO REFRESH EVERY 30 SECONDS =================
    setInterval(() => {
        // prevent refresh while QR scanner is open
        if (!this.scannerOpen) {
            window.location.reload();
        }
    }, 30000); // 30,000 ms = 30 seconds
},

        openScanner() {
            this.scannerOpen = true;
            this.scannerError = '';
            this.scannerLoading = true;

            this.$nextTick(() => {
                this.startScanner();
            });
        },

        async closeScanner() {
            this.scannerOpen = false;
            this.scannerLoading = false;
            this.scannerError = '';

            if (this.qrScannerInstance && this.qrScannerStarted) {
                try {
                    await this.qrScannerInstance.stop();
                } catch (e) {}
            }

            if (this.qrScannerInstance) {
                try {
                    await this.qrScannerInstance.clear();
                } catch (e) {}
            }

            this.qrScannerStarted = false;
            this.qrScannerInstance = null;
        },

        normalizeQrText(decodedText) {
            if (!decodedText) return null;

            let text = String(decodedText).trim();

            text = text.replace(/[\r\n\t ]+/g, '');

            const scanMatch = text.match(/\/scan\/(eSRRS-[A-Z0-9-]+)/i);
            if (scanMatch && scanMatch[1]) {
                return scanMatch[1].toUpperCase();
            }

            const rawMatch = text.match(/(eSRRS-[A-Z0-9-]+)/i);
            if (rawMatch && rawMatch[1]) {
                return rawMatch[1].toUpperCase();
            }

            return null;
        },

        async startScanner() {
            try {
                if (typeof Html5Qrcode === 'undefined') {
                    this.scannerLoading = false;
                    this.scannerError = 'QR scanner library failed to load.';
                    return;
                }

                if (this.qrScannerInstance && this.qrScannerStarted) {
                    return;
                }

                this.qrScannerInstance = new Html5Qrcode('public-qr-reader');

                await this.qrScannerInstance.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                    },
                    async (decodedText) => {
                        const cleanQr = this.normalizeQrText(decodedText);

                        if (!cleanQr) {
                            this.scannerError = 'Invalid QR code. Please scan a valid athlete ID.';
                            return;
                        }

                        try {
                            if (this.qrScannerInstance && this.qrScannerStarted) {
                                await this.qrScannerInstance.stop();
                            }
                        } catch (e) {}

                        window.location.href = "{{ url('/scan') }}/" + encodeURIComponent(cleanQr);
                    },
                    () => {}
                );

                this.qrScannerStarted = true;
                this.scannerLoading = false;
            } catch (error) {
                this.scannerLoading = false;
                this.scannerError = 'Unable to access camera. Please allow camera permission and try again.';
            }
        }
    }
}
</script>

</body>
</html>
