@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')
 {{-- ================= DATA PRIVACY SLIDE-UP ================= --}}
<div id="privacyBanner"
     class="fixed bottom-0 left-0 right-0 z-50 bg-white shadow-2xl border-t border-gray-200
            transform translate-y-full transition-transform duration-500 ease-out">

    <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="text-sm text-gray-700 leading-relaxed">
            <strong class="block text-gray-900 mb-1">Data Privacy Notice</strong>
            In compliance with the Data Privacy Act of 2012 (RA 10173), this system collects and processes
            data strictly for event monitoring and system operations. By continuing, you agree to this use.
        </div>

        <div class="flex gap-3 shrink-0">
            <button onclick="declinePrivacy()"
                    class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition">
                Decline
            </button>

            <button onclick="acceptPrivacy()"
                    class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                Accept
            </button>
        </div>

    </div>
</div>
{{-- ================= END DATA PRIVACY SLIDE-UP ================= --}}


<script>
document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('privacyBanner');

    // Show only once per browser session
    if (!sessionStorage.getItem('privacyAccepted')) {
        setTimeout(() => {
            banner.classList.remove('translate-y-full');
        }, 500);
    }
});

function acceptPrivacy() {
    sessionStorage.setItem('privacyAccepted', 'true');
    closePrivacyBanner();
}

function declinePrivacy() {
    window.location.href = 'about:blank';
}

function closePrivacyBanner() {
    const banner = document.getElementById('privacyBanner');
    banner.classList.add('translate-y-full');
}
</script>


{{-- Banner Slideshow --}}
<div class="relative w-full h-64 md:h-96 overflow-hidden -mt-16 z-0 banner-clip">
    @foreach($banners as $index => $banner)
        <div class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide>

            <img src="{{ asset('storage/' . $banner->image) }}"
                 class="w-full h-full object-cover banner-clip">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/20 banner-clip"></div>

            {{-- Banner Text --}}
            <div class="absolute left-4 sm:left-6 top-1/3 max-w-lg text-white">
                <h2 class="text-4xl sm:text-4xl md:text-6xl font-bold mb-1 sm:mb-2" style="font-family: 'Poppins', sans-serif;">
                    Where Champions Are Made.
                </h2>

                <p class="text-xs sm:text-sm md:text-base text-white/90 leading-snug" style="font-family: 'Poppins', sans-serif;">
                   Follow the action as it unfolds — from opening matches to championship moments.
                </p>
            </div>
        </div>
    @endforeach
</div>

{{-- ================= DASHBOARD STRIP ================= --}}
<section class="relative py-12 bg-gray-100">
    <div class="w-full">

        {{-- Desktop: Small | BIG | Small — Mobile: Stack --}}
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_5fr_2fr] gap-4 items-stretch">

            {{-- ================= LEFT SECTION: QUICK ACTIONS (NBA STYLE) ================= --}}
            <section class="order-2 lg:order-1 h-full">
                <div class="h-full rounded-2xl bg-white p-6 shadow-2xl flex flex-col">

                    <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wide mb-4"
                            style="font-family: 'Poppins', sans-serif;">
                        Quick Stats
                    </h2>

                    <div class="grid grid-cols-1 gap-5 flex-1">

                        {{-- Events --}}
                        <a href="{{ route('viewer.events') }}"
                           class="group bg-black rounded-xl p-5 flex items-center justify-between
                                  hover:bg-black/90 transition shadow-lg">
                            <div>
                                <p class="text-sm uppercase tracking-widest text-gray-400">Events</p>
                                <p class="text-4xl font-extrabold text-white">{{ $eventsCount }}</p>
                            </div>
                            <div class="text-gray-500 group-hover:text-yellow-400 text-3xl font-black">
                                ▸
                            </div>
                        </a>

                        {{-- Games --}}
                        <a href="{{ route('viewer.games') }}"
                           class="group bg-black rounded-xl p-5 flex items-center justify-between
                                  hover:bg-black/90 transition shadow-lg">
                            <div>
                                <p class="text-sm uppercase tracking-widest text-gray-400">Games</p>
                                <p class="text-4xl font-extrabold text-white">{{ $gamesCount }}</p>
                            </div>
                            <div class="text-gray-500 group-hover:text-green-400 text-3xl font-black">
                                ▸
                            </div>
                        </a>

                        {{-- Announcements --}}
                        <a href="{{ route('viewer.announcements') }}"
                           class="group bg-black rounded-xl p-5 flex items-center justify-between
                                  hover:bg-black/90 transition shadow-lg">
                            <div>
                                <p class="text-sm uppercase tracking-widest text-gray-400">News</p>
                                <p class="text-4xl font-extrabold text-white">{{ $announcementsCount }}</p>
                            </div>
                            <div class="text-gray-500 group-hover:text-blue-400 text-3xl font-black">
                                ▸
                            </div>
                        </a>

                    </div>
                </div>
            </section>

            {{-- ================= CENTER SECTION: MEDAL TALLY ================= --}}
<section
    x-data="{
        open: false,
        division: null,
        medals: { gold: [], silver: [], bronze: [] },

        openModal(div, results) {
            this.division = div;
            this.medals = {
                gold: results.filter(r => r.medal === 'Gold'),
                silver: results.filter(r => r.medal === 'Silver'),
                bronze: results.filter(r => r.medal === 'Bronze'),
            };
            this.open = true;
        }
    }"
    class="order-1 lg:order-2 flex flex-col justify-start h-full"
>
    <div class="relative w-full h-full">

        {{-- Medal Tally Background --}}
        <div class="relative bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500 overflow-hidden shadow-lg h-full">

            {{-- Background Image --}}
            <div class="absolute inset-0">
                <img src="{{ asset('storage/medalbg.jpg') }}"
                     class="w-full h-full object-cover brightness-75 blur-sm">
                <div class="absolute inset-0 bg-black/30"></div>
            </div>

            {{-- Scrollable Content --}}
            <div class="relative px-6 py-6 max-h-[600px] overflow-y-auto overflow-x-hidden">

                {{-- Header --}}
                <div class="hidden md:grid grid-cols-[40px_1fr_420px_96px]
                            items-center mb-6 text-white sticky top-0
                            bg-black/20 backdrop-blur-md px-6 py-3
                            rounded-xl max-w-6xl mx-auto z-10">
                    <div></div>
                    <h2 class="text-3xl font-bold">Medal Tally</h2>
                    <div class="grid grid-cols-3 text-center font-semibold text-lg">
                        <span class="text-yellow-400">Gold</span>
                        <span class="text-gray-300">Silver</span>
                        <span class="text-orange-400">Bronze</span>
                    </div>
                    <div class="text-center text-4xl font-bold text-red-600">Total</div>
                </div>

                {{-- Rows --}}
                <div class="space-y-4 md:space-y-6">
                    @foreach($medalTally as $index => $division)

                        {{-- DESKTOP ROW --}}
                        <div
                            @click='openModal(
                                @json($division->only(["id","name"])),
                                @json($division->results)
                            )'
                            class="hidden md:grid grid-cols-[40px_1fr_420px_96px]
                                   items-center p-6 bg-white/10 backdrop-blur-md
                                   rounded-xl shadow-lg cursor-pointer
                                   hover:scale-[1.01] hover:shadow-2xl transition
                                   max-w-6xl mx-auto"
                        >
                            <div class="text-white font-bold text-lg text-center">
                                {{ $index + 1 }}
                            </div>

                            <div class="flex items-center gap-4">
                            @if($division->logo)
                                <img src="{{ asset('storage/' . $division->logo) }}"
                                     class="w-12 h-12 object-cover rounded-full border border-white/30">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                    {{ strtoupper(substr($division->name ?? 'NA',0,2)) }}
                                </div>
                            @endif
                            <span class="text-white font-semibold text-lg">{{ $division->name ?? 'Unknown' }}</span>
                        </div>

                            <div class="grid grid-cols-3 text-center font-extrabold text-4xl">
                                <span class="text-yellow-400">{{ $division->gold }}</span>
                                <span class="text-gray-300">{{ $division->silver }}</span>
                                <span class="text-orange-400">{{ $division->bronze }}</span>
                            </div>

                            <div class="relative text-center">
                                <div class="absolute top-0 bottom-0 left-0 w-[500px] -z-10
                                        bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>
                                <div class="relative text-red-700 font-extrabold text-6xl">
                                    {{ $division->total }}
                                </div>
                            </div>
                        </div>

                        {{-- MOBILE CARD --}}
                        <div
                            @click='openModal(
                                @json($division->only(["id","name"])),
                                @json($division->results)
                            )'
                            class="md:hidden bg-white/10 backdrop-blur-md
                                   rounded-xl shadow-lg p-4 space-y-4
                                   cursor-pointer hover:shadow-xl transition
                                   max-w-md mx-auto"
                        >
                            <div class="flex items-center gap-3">
                            <span class="text-white font-bold text-lg">#{{ $index + 1 }}</span>
                            @if($division->logo)
                                <img src="{{ asset('storage/' . $division->logo) }}"
                                     class="w-10 h-10 object-cover rounded-full border border-white/30">
                            @endif
                            <span class="text-white font-semibold text-base truncate">
                                {{ $division->name ?? 'Unknown' }}
                            </span>
                        </div>

                            <div class="grid grid-cols-3 text-center">
                                <div class="text-yellow-400 text-2xl font-bold">{{ $division->gold }}</div>
                                <div class="text-gray-300 text-2xl font-bold">{{ $division->silver }}</div>
                                <div class="text-orange-400 text-2xl font-bold">{{ $division->bronze }}</div>
                            </div>

                            <div class="relative rounded-lg overflow-hidden">
                            <div class="absolute inset-0
                                        bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>
                            <div class="relative text-center text-red-700 font-extrabold text-4xl py-2">
                                Total: {{ $division->total }}
                            </div>
                        </div>
                        </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ================= MODAL ================= --}}
    <div
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4"
    >
        <div
            @click.away="open = false"
            class="bg-white rounded-2xl shadow-2xl
                   max-w-4xl w-full max-h-[80vh]
                   overflow-y-auto p-6"
        >
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800"
                    x-text="division?.name + ' — Medal Details'"></h3>
                <button @click="open = false" class="text-3xl text-gray-400 hover:text-black">&times;</button>
            </div>

            <template x-for="(list, type) in medals" :key="type">
                <div class="mb-6">
                    <h4 class="text-xl font-bold mb-2"
                        :class="{
                            'text-yellow-600': type === 'gold',
                            'text-gray-600': type === 'silver',
                            'text-orange-600': type === 'bronze'
                        }"
                        x-text="type.toUpperCase()"></h4>

                    <div class="space-y-2">
                        <template x-for="m in list" :key="m.id">
                            <div class="p-3 bg-gray-100 rounded-lg text-sm">
                                <strong x-text="m.event?.name ?? 'Event'"></strong>
                                — <span x-text="m.sport?.name ?? 'Sport'"></span>
                            </div>
                        </template>

                        <p x-show="list.length === 0" class="text-gray-400 italic text-sm">
                            No medals
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>



            {{-- ================= RIGHT SECTION: UPCOMING EVENTS & GAMES (NBA STYLE) ================= --}}
            <section class="order-3 h-full">
                <div class="h-full rounded-2xl bg-white p-6 shadow-2xl flex flex-col">

                    {{-- Upcoming Events --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wide mb-4"
                            style="font-family: 'Poppins', sans-serif;">
                            Upcoming Events
                        </h2>

                        <div class="space-y-3">
                            @forelse($upcomingEvents as $event)
                                <a href="{{ route('viewer.events') }}#event-{{ $event->id }}"
                                   class="block p-4 rounded-xl bg-gray-50 border
                                          hover:bg-gray-100 hover:shadow transition">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-gray-800 truncate">
                                            {{ $event->name }}
                                        </span>
                                        <span class="text-xs font-bold text-gray-500">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d') }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">No upcoming events.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Upcoming Games --}}
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wide mb-4"
                            style="font-family: 'Poppins', sans-serif;">
                            Upcoming Games
                        </h2>

                        <div class="space-y-3">
                            @forelse($upcomingGames as $game)
                                <a href="{{ route('viewer.games') }}#game-{{ $game->id }}"
                                   class="block p-4 rounded-xl bg-gray-50 border
                                          hover:bg-gray-100 hover:shadow transition">
                                    <div class="font-semibold text-gray-800 truncate">
                                        {{ $game->team_a }} <span class="text-gray-400">vs</span> {{ $game->team_b }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y · h:i A') }}
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">No upcoming games.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </div>
</section>
{{-- ================= END DASHBOARD STRIP ================= --}}

{{-- ================= LIVE GAMES ================= --}}
<section class="relative py-16 overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0 -z-10">
        <img src="{{ asset('storage/game1.jpg') }}"
             class="w-full h-full object-cover brightness-75 blur-sm">
        <div class="absolute inset-0 bg-black/70"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">

        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-7xl font-bold text-white tracking-widest uppercase"
                style="font-family: 'Teko', sans-serif;">
                Live Games
            </h2>
            <p class="text-gray-300 mt-2 text-sm md:text-lg">
                Ongoing matches happening right now
            </p>
        </div>

        @if ($liveGames->isEmpty())
            <p class="text-center text-gray-300 text-sm">
                No live games at the moment.
            </p>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                @foreach ($liveGames as $game)

                    @php
                        $divisionA = \App\Models\Division::where('name', $game->team_a)->first();
                        $divisionB = \App\Models\Division::where('name', $game->team_b)->first();
                    @endphp

                    {{-- GAME CARD --}}
                    <a href="{{ route('viewer.game.modal', $game->id) }}"
                       class="group block cursor-pointer bg-white shadow-xl overflow-hidden relative
                              transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">

                        {{-- LIVE BADGE --}}
                        <span class="absolute top-3 right-3 z-20 px-3 py-1 rounded-full
                                     text-xs font-bold bg-red-600 text-white animate-pulse">
                            LIVE
                        </span>

                        {{-- MATCH LAYOUT --}}
                        <div class="grid grid-cols-1 md:grid-cols-[1fr_1.6fr_1fr] h-auto md:h-40">

                            {{-- TEAM A --}}
                            <div class="relative overflow-hidden bg-black h-32 md:h-full">
                                @if($divisionA && $divisionA->logo)
                                    <div class="absolute inset-0 bg-cover bg-center
                                                transition-transform duration-500 group-hover:scale-110"
                                         style="background-image:url('{{ asset('storage/' . $divisionA->logo) }}')"></div>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center text-white">
                                        {{ $game->team_a }}
                                    </div>
                                @endif
                            </div>

                            {{-- CENTER --}}
                            <div class="bg-black text-white flex flex-col items-center justify-center
                                        text-center px-4"
                                 style="font-family: 'Teko', sans-serif;">

                                <p class="text-xs tracking-widest uppercase opacity-80">
                                    {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y') }}
                                </p>

                                <p class="text-4xl md:text-6xl font-bold tracking-wider">
                                    {{ \Carbon\Carbon::parse($game->game_time)->format('h:i A') }}
                                </p>

                                <div class="w-12 h-[2px] bg-white my-2 opacity-80"></div>

                                <p class="text-[10px] uppercase tracking-[0.25em] opacity-70">
                                    {{ $game->event->name }}
                                </p>
                            </div>

                            {{-- TEAM B --}}
                            <div class="relative overflow-hidden bg-black h-32 md:h-full">
                                @if($divisionB && $divisionB->logo)
                                    <div class="absolute inset-0 bg-cover bg-center
                                                transition-transform duration-500 group-hover:scale-110"
                                         style="background-image:url('{{ asset('storage/' . $divisionB->logo) }}')"></div>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center text-white">
                                        {{ $game->team_b }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- TEAM NAMES --}}
                        <div class="grid grid-cols-3 text-center py-2 bg-gray-100"
                             style="font-family: 'Teko', sans-serif;">
                            <span class="font-semibold">{{ $game->team_a }}</span>
                            <span class="text-gray-400 tracking-widest">VS</span>
                            <span class="font-semibold">{{ $game->team_b }}</span>
                        </div>

                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>


{{-- Latest Gallery Highlight --}}
@php
    use App\Models\GalleryPhoto;

    $latestPhoto = GalleryPhoto::latest()->first();

    $remainingPhotos = GalleryPhoto::when($latestPhoto, function ($query) use ($latestPhoto) {
        $query->where('id', '!=', $latestPhoto->id);
    })
    ->latest()
    ->take(10)
    ->get();
@endphp

{{-- =======================
     HIGHLIGHTS / SPOTLIGHT
======================== --}}
@if($latestPhoto)
<section class="bg-gray-50 py-12 md:py-16 border-t">
    <div class="max-w-7xl mx-auto px-4 md:px-6">

        {{-- Section Title --}}
        <div class="text-center mb-8 md:mb-10">
            <h2 class="text-4xl md:text-7xl text-gray-800 font-bold"
                style="font-family: 'Poppins', sans-serif;">
                Highlights
            </h2>
        </div>

        {{-- Spotlight Container --}}
        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-10">

            {{-- LEFT: Slideshow --}}
            <div class="w-full md:flex-1 relative overflow-hidden rounded-2xl md:rounded-3xl shadow-2xl
                        h-64 sm:h-80 md:h-96">

                @if(!empty($latestPhoto->photos))
                    @foreach($latestPhoto->photos as $index => $image)
                        <img
                            src="{{ asset('storage/' . $image) }}"
                            alt="Gallery Image {{ $index + 1 }}"
                            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000
                                   {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                            data-slide
                        >
                    @endforeach

                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                @else
                    <div class="w-full h-full bg-gray-300 flex items-center justify-center
                                text-gray-600 text-lg font-semibold">
                        No Images Found
                    </div>
                @endif
            </div>

            {{-- RIGHT: Title + Description --}}
            <div class="w-full md:flex-1 space-y-4 md:space-y-6
                        text-center md:text-left">

                <h2 class="text-3xl sm:text-4xl md:text-5xl text-gray-800 font-semibold"
                    style="font-family: 'Poppins', sans-serif;">
                    {{ $latestPhoto->title ?? 'Gallery Highlight' }}
                </h2>

                <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                    {{ $latestPhoto->description ?? 'No description available for this gallery item.' }}
                </p>

                <div class="pt-2 flex justify-center md:justify-start">
                    <button
                        onclick="window.location='{{ route('viewer.gallery') }}'"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                               font-semibold rounded-xl shadow-lg transition">
                        View Gallery
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Slideshow Script --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slides = document.querySelectorAll('[data-slide]');
        let current = 0;

        if (slides.length > 1) {
            setInterval(() => {
                slides[current].classList.remove('opacity-100');
                slides[current].classList.add('opacity-0');

                current = (current + 1) % slides.length;

                slides[current].classList.remove('opacity-0');
                slides[current].classList.add('opacity-100');
            }, 4000);
        }
    });
</script>
@endif


{{-- =======================
     REMAINING GALLERY CAROUSEL
======================== --}}
@if($remainingPhotos->isNotEmpty())
<section class="bg-gray-50 py-12 border-t relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 md:px-6">

        <div class="relative">

            {{-- Left Arrow (hidden on mobile) --}}
            <button
                class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2
                       bg-white/80 hover:bg-white text-gray-800
                       rounded-full p-3 shadow-lg z-20"
                onclick="document.getElementById('gallery-carousel')
                    .scrollBy({ left: -300, behavior: 'smooth' })">
                &#8592;
            </button>

            {{-- Right Arrow (hidden on mobile) --}}
            <button
                class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2
                       bg-white/80 hover:bg-white text-gray-800
                       rounded-full p-3 shadow-lg z-20"
                onclick="document.getElementById('gallery-carousel')
                    .scrollBy({ left: 300, behavior: 'smooth' })">
                &#8594;
            </button>

            {{-- Carousel --}}
            <div id="gallery-carousel"
                 class="flex space-x-6 overflow-x-auto scroll-smooth py-2">

                @foreach($remainingPhotos as $photo)
                    @php
                        $firstImage = $photo->photos[0] ?? null;
                        $extraCount = max(count($photo->photos) - 1, 0);
                    @endphp

                    <div
                        class="relative flex-shrink-0 w-64 md:w-72 rounded-2xl shadow-lg cursor-pointer
                               transform hover:scale-105 transition-all duration-500 group"
                        onclick="openPhotoModal({{ $photo->id }})">

                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage) }}"
                                 class="w-full h-64 md:h-72 object-cover rounded-2xl">

                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100
                                        transition-opacity duration-500 flex flex-col justify-center items-center rounded-2xl">
                                @if($extraCount > 0)
                                    <div class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full
                                                text-white font-semibold text-sm mb-2">
                                        +{{ $extraCount }} More
                                    </div>
                                @endif
                                <div class="text-white text-lg font-bold text-center">
                                    {{ $photo->title ?? 'Gallery Item' }}
                                </div>
                            </div>
                        @else
                            <div class="h-64 md:h-72 bg-gray-200 flex items-center justify-center
                                        text-gray-500 text-lg font-semibold rounded-2xl">
                                No Image
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</section>
@endif




{{-- Latest Announcements --}}
<section class="bg-gray-200 py-12 border-t">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Section Header --}}
        <div class="text-center mb-10">
            <h2 class="text-4xl md:text-7xl text-gray-800 font-bold" style="font-family: 'Poppins', sans-serif;">
                Latest News & Updates
            </h2>
            <p class="text-gray-500 mt-2 text-sm md:text-lg">Stay informed with the latest announcements and results</p>
        </div>

        {{-- Announcements Grid --}}
        @if($latestAnnouncements->isEmpty())
            <p class="text-center text-gray-600 text-sm">No announcements available.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latestAnnouncements as $announcement)
                    <a href="{{ route('viewer.announcements') }}#announcement-{{ $announcement->id }}" 
                       class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-5 flex flex-col justify-between cursor-pointer group">

                        {{-- Content --}}
                        <div>
                            <h4 class="text-lg md:text-xl text-gray-800 group-hover:text-blue-600 transition mb-1" style="font-family: 'Poppins', sans-serif;">
                                {{ $announcement->title }}
                            </h4>
                            <p class="text-gray-400 text-xs mb-2">{{ $announcement->published_at->format('M d, Y') }}</p>
                            <p class="text-gray-600 text-sm">{{ \Illuminate\Support\Str::limit($announcement->content, 120, '...') }}</p>
                        </div>

                        {{-- Hover overlay for NBA-style subtle effect --}}
                        <div class="absolute inset-0 bg-blue-50/20 opacity-0 group-hover:opacity-100 transition rounded-2xl pointer-events-none"></div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>



{{-- Banner Slideshow JS --}}
<script>
let slides = document.querySelectorAll('[data-slide]');
let current = 0;
function showSlide(index){
    slides.forEach((slide,i)=>slide.style.opacity = i===index?'1':'0');
}
setInterval(()=>{ current=(current+1)%slides.length; showSlide(current); },5000);
</script>

{{-- Gallery Modal JS --}}
<script>
const photosData = @json($photos ?? []);
let currentPhotoIndex = 0;
let currentPhotoSet = null;

function openPhotoModal(id) {
    const photo = photosData.find(p=>p.id===id);
    if(!photo) return;
    currentPhotoIndex = 0;
    currentPhotoSet = photo;
    const modal = document.createElement('div');
    modal.id = 'photoModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full relative flex flex-col">
            <button onclick="closePhotoModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            <div class="p-6 flex flex-col overflow-y-auto max-h-[90vh]" id="modalContent"></div>
        </div>
    `;
    document.body.appendChild(modal);
    renderImage();
}

function renderImage(){
    const content = document.getElementById('modalContent');
    if(!currentPhotoSet) return;
    let html = `<h2 class="text-xl font-semibold mb-2">${currentPhotoSet.title}</h2>`;
    html += `<p class="text-gray-600 mb-4">${currentPhotoSet.description}</p>`;
    html += `<div class="relative w-full h-96 flex items-center justify-center mb-4">`;
    html += `<img src="/storage/${currentPhotoSet.photos[currentPhotoIndex]}" class="w-full h-full object-cover rounded">`;
    if(currentPhotoSet.photos.length>1){
        html += `<div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">${currentPhotoIndex+1} / ${currentPhotoSet.photos.length}</div>`;
    }
    html += `</div>`;
    content.innerHTML = html;
}

function closePhotoModal(){
    const modal = document.getElementById('photoModal');
    if(modal) modal.remove();
    currentPhotoIndex=0;
    currentPhotoSet=null;
}
</script>

@endsection
