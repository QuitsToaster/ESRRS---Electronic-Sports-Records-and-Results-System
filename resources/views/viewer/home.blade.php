@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')
 {{-- ================= DATA PRIVACY SLIDE-UP ================= --}}
<div id="privacyBanner"
     class="fixed bottom-0 left-0 right-0 z-50 bg-black shadow-2xl border-t border-black
            transform translate-y-full transition-transform duration-500 ease-out">

    <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="text-sm text-gray-100 leading-relaxed">
            <strong class="block text-white mb-1">Data Privacy Notice</strong>
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

{{-- ====== Event Status Cards ====== --}}
@php
    $statusCounts = [
        'Upcoming' => $allUpcomingEvents->where('status', 'Upcoming'),
        'Ongoing'  => $allUpcomingEvents->where('status', 'Ongoing'),
        'Finished' => $allUpcomingEvents->where('status', 'Completed'),
        'Delayed'  => $allUpcomingEvents->where('status', 'Delayed'),
    ];
@endphp

<div x-data="{ activeStatus: null }" class="max-w-full mx-auto px-4 py-8 space-y-6 bg-gray-100">

    <h2 class="text-xl sm:text-4xl font-bold text-black" style="font-family: 'Poppins', sans-serif;">Event Stats</h2>
    <p class="text-gray-900 mt-2" style="font-family: 'Poppins', sans-serif;">Click card to view event details.</p>

    {{-- ====== Status Cards ====== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach($statusCounts as $status => $events)
            <div @click="activeStatus = '{{ $status }}'"
                 class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center text-black cursor-pointer transition hover:scale-105">
                <div class="text-2xl sm:text-3xl font-bold">{{ $events->count() }}</div>
                <div class="mt-1 text-sm sm:text-base font-semibold">{{ $status }} Event</div>
            </div>
        @endforeach
    </div>

    {{-- ===== Modal for Events by Status ===== --}}
    <div x-show="activeStatus" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4" style="display: none;">
        <div @click.away="activeStatus = null" class="bg-white w-full max-w-3xl rounded-xl shadow-xl overflow-auto max-h-[80vh]">
            {{-- Modal Header --}}
            <div class="flex justify-between items-center p-4 border-b border-gray-300">
                <h3 class="text-xl font-bold text-black" x-text="activeStatus + ' Events'"></h3>
                <button @click="activeStatus = null" class="text-black text-2xl hover:text-gray-600">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 space-y-3 text-black">
                @foreach($statusCounts as $status => $events)
                    <template x-if="activeStatus === '{{ $status }}'">
                        <div class="space-y-2">
                            @forelse($events as $event)
                                <div class="p-3 bg-gray-100 rounded shadow flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                    <div>
                                        <div class="font-semibold">{{ $event->name }}</div>
                                        <div class="text-sm text-gray-700">{{ $event->start_date?->format('M d, Y') ?? 'TBA' }} | {{ $event->venue ?? 'TBA' }}</div>
                                        @if($event->status_reason)
                                            <div class="text-xs text-red-600 mt-1">Reason: {{ $event->status_reason }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 sm:mt-0 text-sm font-medium text-gray-800">{{ $status }}</div>
                                </div>
                            @empty
                                <div class="text-gray-500 italic text-center py-4">No events available.</div>
                            @endforelse
                        </div>
                    </template>
                @endforeach
            </div>
        </div>
    </div>

</div>


<style>
    .gold-shimmer {
    background: linear-gradient(
        90deg,
        #a16207,
        #fde047,
        #facc15,
        #fde047,
        #a16207
    );
    background-size: 200% auto;
    color: transparent;
    background-clip: text;
    -webkit-background-clip: text;
    animation: shimmer 2.5s linear infinite;
}

@keyframes shimmer {
    to {
        background-position: 200% center;
    }
}

</style>

{{-- ================= DASHBOARD STRIP ================= --}}
<section class="relative py-12 bg-gray-100">
    <div class="w-full">
        <h1 class="text-7xl font-extrabold text-center gold-shimmer uppercase tracking-wide mb-4"
            style="font-family: 'Poppins', sans-serif;">
            Medal Tally
        </h1>

        {{-- Desktop: Small | BIG | Small — Mobile: Stack --}}
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_5fr_2fr] gap-4 items-stretch">

          {{-- ================= LEFT: LATEST NEWS & ANNOUNCEMENTS ================= --}}
<section
    class="order-2 lg:order-1 h-full"
    x-data="{
        search: '',
        allAnnouncements: {{ $allAnnouncements->toJson() }},
        latestAnnouncements: {{ $latestAnnouncements->toJson() }},
    }"
>
    <div class="h-full bg-white p-6 shadow-2xl flex flex-col">

        <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wide mb-4"
            style="font-family: 'Poppins', sans-serif;">
            Latest News & Announcements
        </h2>

        {{-- Search Bar --}}
        <input
            type="text"
            placeholder="Search announcements..."
            x-model="search"
            class="mb-4 w-full rounded-lg border border-gray-300 p-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-400"
        >

        <div class="space-y-4 overflow-y-auto pr-1 flex-1">

            <template
                x-for="announcement in (
                    search
                        ? allAnnouncements.filter(a =>
                            a.title.toLowerCase().includes(search.toLowerCase()) ||
                            a.content.toLowerCase().includes(search.toLowerCase())
                          )
                        : latestAnnouncements
                ).slice(0, 2)"
                :key="announcement.id"
            >
                <a
                    :href="'{{ route('viewer.announcements') }}#announcement-' + announcement.id"
                    class="group block rounded-xl border bg-gray-50 p-4
                           hover:bg-gray-100 hover:shadow transition relative"
                >
                    <h4 class="font-semibold text-gray-800 group-hover:text-blue-600 transition"
                        style="font-family: 'Poppins', sans-serif;"
                        x-text="announcement.title">
                    </h4>

                    <p class="text-xs text-gray-400 mt-1"
                       x-text="new Date(announcement.published_at)
                           .toLocaleDateString('en-US', {
                               month: 'short',
                               day: '2-digit',
                               year: 'numeric'
                           })">
                    </p>

                    <p class="text-sm text-gray-600 mt-2"
                       x-text="
                           announcement.content.length > 100
                               ? announcement.content.substring(0,100) + '...'
                               : announcement.content
                       ">
                    </p>

                    <div class="absolute inset-0 bg-blue-50/20 opacity-0
                                group-hover:opacity-100 transition
                                rounded-xl pointer-events-none"></div>
                </a>
            </template>

            <template x-if="search && allAnnouncements.length === 0">
                <p class="text-sm text-gray-500">No announcements found.</p>
            </template>

        </div>
    </div>
</section>


          {{-- ================= CENTER SECTION: MEDAL TALLY ================= --}}
<section
    x-data="{
        activeTab: 'regular',
        open: false,
        division: null,
        medals: { gold: [], silver: [], bronze: [] },

        switchTab(tab) { this.activeTab = tab },
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
            <div class="absolute inset-0">
                <img src="{{ asset('storage/medalbg.jpg') }}" class="w-full h-full object-cover brightness-75 blur-sm">
                <div class="absolute inset-0 bg-black/30"></div>
            </div>

            <div class="relative px-6 py-6 max-h-[600px] overflow-y-auto overflow-x-hidden">

                {{-- Tabs --}}
                <div class="flex gap-4 mb-6 text-white font-semibold">
                    <button
                        :class="{'bg-black/40': activeTab==='regular','bg-black/20': activeTab!=='regular'}"
                        class="px-4 py-2 rounded"
                        @click="switchTab('regular')">Regular</button>
                    <button
                        :class="{'bg-black/40': activeTab==='para','bg-black/20': activeTab!=='para'}"
                        class="px-4 py-2 rounded"
                        @click="switchTab('para')">Para Games</button>
                </div>

                {{-- Medal Rows --}}
                <div class="space-y-4 md:space-y-6">

                    {{-- Regular Medal Tally --}}
                    <template x-if="activeTab === 'regular'">
                        <div>
                            @foreach($medalTally as $index => $division)
                                @include('viewer.partials.medal-row', ['division' => $division, 'index' => $index])
                            @endforeach
                        </div>
                    </template>

                    {{-- Para Medal Tally --}}
                    <template x-if="activeTab === 'para'">
                        <div>
                            @foreach($medalTallyPara as $index => $division)
                                @include('viewer.partials.medal-row', ['division' => $division, 'index' => $index])
                            @endforeach
                        </div>
                    </template>

                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODAL ================= --}}
    <div
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
    >
        <div @click.away="open = false"
             class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[80vh] overflow-y-auto p-6 border border-gray-200">
             {{-- Header --}}
             <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-3">
                <h3 class="text-2xl font-semibold text-gray-800" x-text="division?.name + ' — Medal Details'"></h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
             </div>

             {{-- Medal Sections --}}
             <template x-for="(list, type) in medals" :key="type">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-3 flex items-center gap-2"
                        :class="{
                            'text-yellow-600': type === 'gold',
                            'text-gray-600': type === 'silver',
                            'text-orange-500': type === 'bronze'
                        }">
                        <span x-text="type.toUpperCase()"></span>
                        <span class="text-sm text-gray-400" x-text="list.length + ' medals'"></span>
                    </h4>

                    <div class="space-y-2">
                        <template x-for="m in list" :key="m.id">
                            <div class="bg-gray-50 p-3 rounded-lg shadow-sm flex flex-col gap-1 hover:shadow-md transition">
                                <div class="flex justify-between items-center">
                                    <strong class="text-gray-800" x-text="m.event?.name ?? 'Event'"></strong>
                                    <span class="text-gray-500 text-sm font-bold" x-text="m.sport?.name ?? 'Sport'"></span>
                                </div>
                                <div class="flex flex-wrap text-xs text-gray-500 gap-3 mt-1">
                                    <span>Level: <strong x-text="m.event?.age_group ?? 'N/A'"></strong></span>
                                    <span>Category: <strong x-text="m.event?.category ?? 'N/A'"></strong></span>
                                    <span>Schedule: <strong x-text="m.event?.start_date ? new Date(m.event.start_date).toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'}) : 'N/A'"></strong></span>
                                    <span>Award Date: <strong x-text="m.event?.award_date ? new Date(m.event.award_date).toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'}) : 'N/A'"></strong></span>
                                </div>
                            </div>
                        </template>

                        <p x-show="list.length === 0" class="text-gray-400 italic text-sm">No medals</p>
                    </div>
                </div>
             </template>
        </div>
    </div>
</section>


            {{-- ================= RIGHT SECTION: UPCOMING EVENTS & GAMES ================= --}}
<section
    class="order-3 h-full"
    x-data="{
        eventSearch: '',
        gameSearch: '',

        allEvents: {{ $allUpcomingEvents->toJson() }},
        latestEvents: {{ $latestUpcomingEvents->toJson() }},

        allGames: {{ $allUpcomingGames->toJson() }},
        latestGames: {{ $latestUpcomingGames->toJson() }},
    }"
>
    <div class="h-full bg-white p-6 shadow-2xl flex flex-col">

        {{-- ================= UPCOMING EVENTS ================= --}}
        <div class="mb-8">
            <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wide mb-4">
                Upcoming Events
            </h2>

            <input
                type="text"
                placeholder="Search events..."
                x-model="eventSearch"
                class="mb-4 w-full rounded-lg border border-gray-300 p-2 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <div class="space-y-3">
                <template
                    x-for="event in (
                        eventSearch
                            ? allEvents.filter(e =>
                                e.name.toLowerCase().includes(eventSearch.toLowerCase())
                              )
                            : latestEvents
                    ).slice(0, 2)"
                    :key="event.id"
                >
                    <a
                        :href="'{{ route('viewer.events') }}#event-' + event.id"
                        class="block p-4 rounded-xl bg-gray-50 border
                               hover:bg-gray-100 hover:shadow transition"
                    >
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-800 truncate"
                                  x-text="event.name"></span>

                            <span class="text-xs font-bold text-gray-500"
                                  x-text="new Date(event.start_date)
                                      .toLocaleDateString('en-US', {
                                          month: 'short',
                                          day: '2-digit'
                                      })"></span>
                        </div>

                        <div class="mt-1 text-xs text-gray-500 space-y-1">
                            <p>Sport: <span x-text="event.sport?.name ?? 'N/A'"></span></p>
                            <p>Level: <span x-text="event.age_group ?? 'N/A'"></span></p>
                            <p>Category: <span x-text="event.category ?? 'N/A'"></span></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>
        </div>
    </div>
</section>
{{-- ================= END DASHBOARD STRIP ================= --}}


<!-- {{-- ================= LIVE GAMES ================= --}}
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
                Ongoing Games
            </h2>
            <p class="text-gray-300 mt-2 text-sm md:text-lg">
                Ongoing matches happening right now
            </p>
        </div>

        @if ($liveGames->isEmpty())
            <p class="text-center text-gray-300 text-sm">
                No ongoing games at the moment.
            </p>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                @foreach ($liveGames as $game)

                    @php
                        $divisionA = \App\Models\Division::where('name', $game->team_a)->first();
                        $divisionB = \App\Models\Division::where('name', $game->team_b)->first();
                    @endphp

                    {{-- GAME CARD (NO CLICK / NO REDIRECT) --}}
                    <div
                        class="group bg-white shadow-xl overflow-hidden relative
                               transition-all duration-300">

                        {{-- LIVE BADGE --}}
                        <span class="absolute top-3 right-3 z-20 px-3 py-1 rounded-full
                                     text-xs font-bold bg-red-600 text-white animate-pulse">
                            Ongoing
                        </span>

                        {{-- MATCH LAYOUT --}}
                        <div class="grid grid-cols-1 md:grid-cols-[1fr_1.6fr_1fr] h-auto md:h-40">

                            {{-- TEAM A --}}
                            <div class="relative overflow-hidden bg-black h-32 md:h-full">
                                @if($divisionA && $divisionA->logo)
                                    <div class="absolute inset-0 bg-cover bg-center"
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
                                    <div class="absolute inset-0 bg-cover bg-center"
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

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section> -->



<!-- {{-- Latest Gallery Highlight --}}
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

        <div class="text-center mb-8 md:mb-10">
            <h2 class="text-4xl md:text-7xl text-gray-800 font-bold"
                style="font-family: 'Poppins', sans-serif;">
                Highlights
            </h2>
        </div>

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

                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                @else
                    <div class="w-full h-full bg-gray-300 flex items-center justify-center
                                text-gray-600 text-lg font-semibold">
                        No Images Found
                    </div>
                @endif
            </div>

            {{-- RIGHT --}}
            <div class="w-full md:flex-1 space-y-4 md:space-y-6 text-center md:text-left">

                <h2 class="text-3xl sm:text-4xl md:text-5xl text-gray-800 font-semibold"
                    style="font-family: 'Poppins', sans-serif;">
                    {{ $latestPhoto->title ?? 'Gallery Highlight' }}
                </h2>

                <p class="text-gray-600 text-base md:text-lg leading-relaxed">
                    {{ $latestPhoto->description ?? 'No description available for this gallery item.' }}
                </p>

                <div class="pt-2 flex justify-center md:justify-start">
                    <a
                        href="{{ route('viewer.gallery') }}"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white
                               font-semibold rounded-xl shadow-lg transition">
                        View Gallery
                    </a>
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
                slides[current].classList.replace('opacity-100', 'opacity-0');
                current = (current + 1) % slides.length;
                slides[current].classList.replace('opacity-0', 'opacity-100');
            }, 4000);
        }
    });
</script>
@endif


{{-- =======================
     REMAINING GALLERY CAROUSEL (CLICKABLE)
======================== --}}
@if($remainingPhotos->isNotEmpty())
<section class="bg-gray-50 py-12 border-t relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 md:px-6">

        <div class="relative">

            {{-- Left Arrow --}}
            <button
                class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2
                       bg-white/80 hover:bg-white text-gray-800
                       rounded-full p-3 shadow-lg z-20"
                onclick="document.getElementById('gallery-carousel')
                    .scrollBy({ left: -300, behavior: 'smooth' })">
                &#8592;
            </button>

            {{-- Right Arrow --}}
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

                    <a
                        href="{{ route('viewer.gallery') }}"
                        class="relative flex-shrink-0 w-64 md:w-72 rounded-2xl shadow-lg
                               transform hover:scale-105 transition-all duration-500 group"
                    >
                        @if($firstImage)
                            <img
                                src="{{ asset('storage/' . $firstImage) }}"
                                class="w-full h-64 md:h-72 object-cover rounded-2xl"
                            >

                            <div class="absolute inset-0 bg-black/30 opacity-0
                                        group-hover:opacity-100 transition-opacity duration-500
                                        flex flex-col justify-center items-center rounded-2xl">

                                @if($extraCount > 0)
                                    <div class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full
                                                text-white font-semibold text-sm mb-2">
                                        +{{ $extraCount }} More
                                    </div>
                                @endif

                                <div class="text-white text-lg font-bold text-center px-2">
                                    {{ $photo->title ?? 'Gallery Item' }}
                                </div>
                            </div>
                        @else
                            <div class="h-64 md:h-72 bg-gray-200 flex items-center justify-center
                                        text-gray-500 text-lg font-semibold rounded-2xl">
                                No Image
                            </div>
                        @endif
                    </a>

                @endforeach

            </div>
        </div>
    </div>
</section>
@endif -->



{{-- Banner Slideshow JS --}}
<script>
let slides = document.querySelectorAll('[data-slide]');
let current = 0;
function showSlide(index){
    slides.forEach((slide,i)=>slide.style.opacity = i===index?'1':'0');
}
setInterval(()=>{ current=(current+1)%slides.length; showSlide(current); },5000);
</script>

<!-- {{-- Gallery Modal JS --}}
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
</script> -->

@endsection
