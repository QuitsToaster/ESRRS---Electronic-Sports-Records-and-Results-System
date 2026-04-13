@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')
 {{-- ================= DATA PRIVACY MODAL ================= --}}
<div id="privacyModal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm
            opacity-0 pointer-events-none transition-opacity duration-300">

    <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6
                transform scale-95 transition-transform duration-300"
         id="privacyModalContent">

        <h3 class="text-lg font-bold text-gray-800 mb-2">
            Data Privacy Notice
        </h3>

        <p class="text-sm text-gray-600 leading-relaxed mb-4">
            In compliance with the Data Privacy Act of 2012 (RA 10173), this system collects and processes
            data strictly for event monitoring and system operations. By continuing, you agree to this use.
        </p>

        <div class="flex justify-end gap-3">
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
{{-- ================= END DATA PRIVACY MODAL ================= --}}


<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('privacyModal');
    const content = document.getElementById('privacyModalContent');

    if (!sessionStorage.getItem('privacyAccepted')) {
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 300);
    }
});

function acceptPrivacy() {
    sessionStorage.setItem('privacyAccepted', 'true');
    closePrivacyModal();
}

function declinePrivacy() {
    window.location.href = 'about:blank';
}

function closePrivacyModal() {
    const modal = document.getElementById('privacyModal');
    const content = document.getElementById('privacyModalContent');

    modal.classList.add('opacity-0', 'pointer-events-none');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
}
</script>


{{-- Banner Slideshow --}}
<div class="relative w-full h-64 md:h-96 overflow-hidden -mt-16 z-0 banner-clip">
    @foreach($banners as $index => $banner)
        <div class="absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-slide>

            <img src="{{ asset('storage/' . $banner->image) }}"
                 class="w-full h-full object-cover banner-clip">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 to-black/10 banner-clip"></div>

            {{-- Banner Text --}}
            <div class="absolute left-4 sm:left-6 top-1/3 max-w-lg text-white">
                <h2 class="text-4xl sm:text-4xl md:text-6xl font-bold mb-1 sm:mb-2" style="font-family: 'Poppins', sans-serif;">
                    CAVRAA MEET 2026:
                </h2>

                <p class="text-xl sm:text-3xl text-white/90 leading-snug font-style: italic" style="font-family: 'Poppins', sans-serif;">
                   KAISAng Lakas ng Isports sa Dos.
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
        'Finished' => $allUpcomingEvents->where('status', 'Finished'),
        'Delayed'  => $allUpcomingEvents->where('status', 'Delayed'),
    ];
@endphp

<div x-data="{ activeStatus: null }" class="max-w-full mx-auto px-4 py-8 space-y-6 bg-gray-50">
    
    {{-- Header with accent line --}}
    <div class="text-center md:text-left">
        <div class="inline-block">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif;">
                Event Stats
            </h2>
            <div class="h-1 w-20 bg-blue-600 mt-2 mx-auto md:mx-0"></div>
        </div>
        <p class="text-gray-600 mt-3 text-sm sm:text-base" style="font-family: 'Poppins', sans-serif;">
            Click any card to view detailed event information
        </p>
    </div>

    {{-- ====== Status Cards ====== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $statusColors = [
                'Upcoming' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'hover' => 'hover:border-blue-300'],
                'Ongoing' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'hover' => 'hover:border-green-300'],
                'Finished' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'hover' => 'hover:border-gray-300'],
                'Delayed' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'hover' => 'hover:border-red-300'],
            ];
        @endphp
        
        @foreach($statusCounts as $status => $events)
            @php $colors = $statusColors[$status] ?? $statusColors['Upcoming']; @endphp
            <div @click="activeStatus = '{{ $status }}'"
                 class="{{ $colors['bg'] }} border-2 {{ $colors['border'] }} rounded-xl p-5 flex flex-col items-center justify-center cursor-pointer transition-all duration-200 hover:scale-105 hover:shadow-md {{ $colors['hover'] }} group">
                <span class="text-3xl sm:text-4xl font-black {{ $colors['text'] }}">{{ $events->count() }}</span>
                <span class="mt-2 text-sm sm:text-base font-semibold {{ $colors['text'] }}">{{ $status }}</span>
                <span class="text-xs text-gray-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">Click to view</span>
            </div>
        @endforeach
    </div>

    {{-- ===== Modal for Events by Status ===== --}}
    <div x-show="activeStatus" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" 
         style="display: none;">
        
        <div @click.away="activeStatus = null"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white w-full max-w-3xl rounded-xl shadow-2xl overflow-auto max-h-[80vh]">
            
            {{-- Modal Header --}}
            <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                    <h3 class="text-xl font-bold text-gray-900" x-text="activeStatus + ' Events'"></h3>
                </div>
                <button @click="activeStatus = null" 
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1 hover:bg-gray-200 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-3">
                @foreach($statusCounts as $status => $events)
                    <template x-if="activeStatus === '{{ $status }}'">
                        <div class="space-y-3">
                            @forelse($events as $event)
                                <a href="{{ route('viewer.events', ['event' => $event->id]) }}" 
                                   class="block p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-300 hover:shadow-md transition-all duration-200 group">
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        {{-- Event Details --}}
                                        <div class="flex-1">
                                            {{-- Sport Name Badge --}}
                                            <div class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold mb-2 border border-blue-200">
                                                {{ $event->sport->name ?? 'No Sport Assigned' }}
                                            </div>
                                            
                                            {{-- Event Name --}}
                                            <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-700 transition-colors">
                                                {{ $event->name }}
                                            </h4>
                                            
                                            {{-- Event Meta --}}
                                            <div class="flex flex-wrap gap-3 mt-2 text-sm">
                                                <span class="flex items-center gap-1 text-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ $event->age_group }}
                                                </span>
                                                <span class="flex items-center gap-1 text-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                    </svg>
                                                    {{ $event->category }}
                                                </span>
                                                <span class="flex items-center gap-1 text-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $event->start_date?->format('M d, Y') ?? 'TBA' }}
                                                </span>
                                                <span class="flex items-center gap-1 text-gray-600">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    {{ $event->venue ?? 'TBA' }}
                                                </span>
                                            </div>

                                            {{-- Status Reason --}}
                                            @if($event->status_reason)
                                                <div class="mt-2 text-xs text-red-600 bg-red-50 inline-block px-2 py-1 rounded-full border border-red-200">
                                                    ⚠️ {{ $event->status_reason }}
                                                </div>
                                            @endif

                                            {{-- Game Winners --}}
                                            @if($event->status === 'Finished' && !empty($event->game_winners))
                                                <div class="mt-3 pt-2 border-t border-gray-100">
                                                    <span class="text-xs font-semibold text-gray-700">Game Winners:</span>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        @foreach($event->game_winners as $index => $divisionId)
                                                            @if($divisionId)
                                                                @php $division = \App\Models\Division::find($divisionId); @endphp
                                                                <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded-full text-xs border border-green-200">
                                                                    Game {{ $index + 1 }}: {{ $division->name }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Status Badge --}}
                                        <div class="sm:text-right">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                                @if($status === 'Upcoming') bg-blue-100 text-blue-700
                                                @elseif($status === 'Ongoing') bg-green-100 text-green-700
                                                @elseif($status === 'Finished') bg-gray-100 text-gray-700
                                                @elseif($status === 'Delayed') bg-red-100 text-red-700
                                                @endif">
                                                {{ $status }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500 italic">No {{ $status }} events available.</p>
                                </div>
                            @endforelse
                        </div>
                    </template>
                @endforeach
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 border-t border-gray-200 bg-gray-50 text-xs text-gray-500 flex justify-between">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Click any event for full details
                </span>
                <span>Click outside or ESC to close</span>
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
        <div class="w-full">


    {{-- ================= CENTER SECTION: MEDAL TALLY ================= --}}
<section
    class="order-1 lg:order-2 flex flex-col justify-start h-full"
    x-data="{
        activeTab: 'regular',
        open: false,
        division: null,
        medals: { gold: [], silver: [], bronze: [] },
        currentTime: '',

        switchTab(tab) { this.activeTab = tab },

        openModal(div, results) {
            this.division = div;
            this.medals = {
                gold: results.filter(r => r.medal === 'Gold'),
                silver: results.filter(r => r.medal === 'Silver'),
                bronze: results.filter(r => r.medal === 'Bronze'),
            };
            this.open = true;
        },

        initClock() {
            const update = () => {
                const now = new Date();
                this.currentTime = now.toLocaleString('en-PH', {
                    timeZone: 'Asia/Manila',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });
            };
            update();
            setInterval(update, 1000);
        }
    }"
    x-init="initClock()"
>
@php
use App\Models\AllAwardResult;
use App\Models\Division;

/*
|--------------------------------------------------------------------------
| ALL DIVISIONS
|--------------------------------------------------------------------------
*/
$divisions = Division::orderBy('name')->get();

/*
|--------------------------------------------------------------------------
| REGULAR MEDAL TALLY
|--------------------------------------------------------------------------
*/
$regularRaw = AllAwardResult::select('sdo','medal', \DB::raw('SUM(`count`) as total'))
    ->where('status', 'approved')
    ->whereIn('division', ['Elementary', 'Secondary'])
    ->groupBy('sdo','medal')
    ->get()
    ->groupBy('sdo');

$medalTallyRegular = $divisions->map(function($division) use ($regularRaw) {
    $rows = $regularRaw->get($division->name, collect());

    $gold   = $rows->where('medal','Gold')->sum('total');
    $silver = $rows->where('medal','Silver')->sum('total');
    $bronze = $rows->where('medal','Bronze')->sum('total');

    return (object)[
        'id'     => $division->id, // ✅ FIX ADDED
        'name'   => $division->name,
        'logo'   => $division->logo,
        'gold'   => $gold,
        'silver' => $silver,
        'bronze' => $bronze,
        'total'  => $gold + $silver + $bronze,
        'results'=> AllAwardResult::with(['sport','sportType'])
        ->where('status','approved')
        ->whereIn('division',['Elementary','Secondary'])
        ->where('sdo',$division->name)
        ->get()
        ->map(function($r){
            return [
                'id' => $r->id,
                'sport' => $r->sport,
                'sportType' => $r->sportType, // ✅ FORCE INCLUDE
                'type_id' => $r->type_id,
                'division' => $r->division,
                'category' => $r->category,
                'created_at' => $r->created_at,
                'medal' => $r->medal,
            ];
        }),
    ];
});

/*
|--------------------------------------------------------------------------
| PARA MEDAL TALLY
|--------------------------------------------------------------------------
*/
$paraRaw = AllAwardResult::select('sdo','medal', \DB::raw('SUM(`count`) as total'))
    ->where('status', 'approved')
    ->where('division', 'Paragames')
    ->groupBy('sdo','medal')
    ->get()
    ->groupBy('sdo');

$medalTallyPara = $divisions->map(function($division) use ($paraRaw) {
    $rows = $paraRaw->get($division->name, collect());

    $gold   = $rows->where('medal','Gold')->sum('total');
    $silver = $rows->where('medal','Silver')->sum('total');
    $bronze = $rows->where('medal','Bronze')->sum('total');

    return (object)[
        'id'     => $division->id, // ✅ FIX ADDED
        'name'   => $division->name,
        'logo'   => $division->logo,
        'gold'   => $gold,
        'silver' => $silver,
        'bronze' => $bronze,
        'total'  => $gold + $silver + $bronze,
        'results'=> AllAwardResult::with(['sport','sportType'])
        ->where('status','approved')
        ->whereIn('division',['Elementary','Secondary'])
        ->where('sdo',$division->name)
        ->get()
        ->map(function($r){
            return [
                'id' => $r->id,
                'sport' => $r->sport,
                'sportType' => $r->sportType, // ✅ FORCE INCLUDE
                'type_id' => $r->type_id,
                'division' => $r->division,
                'category' => $r->category,
                'created_at' => $r->created_at,
                'medal' => $r->medal,
            ];
        }),
    ];
});

/*
|--------------------------------------------------------------------------
| SORTING
|--------------------------------------------------------------------------
*/
$sortFn = fn($a,$b) =>
    $b->gold <=> $a->gold
    ?: $b->silver <=> $a->silver
    ?: $b->bronze <=> $a->bronze;

$medalTallyRegular = $medalTallyRegular->sort($sortFn)->values();
$medalTallyPara    = $medalTallyPara->sort($sortFn)->values();
@endphp

<div class="relative w-full h-full">
    <div class="relative bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500 overflow-hidden shadow-lg h-full">
        <div class="absolute inset-0">
            <img src="{{ asset('storage/medalbg.jpg') }}" class="w-full h-full object-cover brightness-75 blur-sm">
            <div class="absolute inset-0 bg-black/30"></div>
        </div>

        <div class="relative px-6 py-6 max-h-[900px] flex flex-col overflow-hidden">

       {{-- ================= HEADER: REAL-TIME (MODERN & NOTICEABLE) ================= --}}
<div class="text-center mb-6 relative z-10">
    {{-- Status Badge --}}
    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 mb-3 border border-white/20 shadow-lg">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
        </span>
        <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Live Updates</span>
    </div>

    {{-- Main Title --}}
    <h2 class="text-2xl md:text-3xl font-black tracking-tighter uppercase bg-gradient-to-r from-yellow-200 via-yellow-400 to-amber-500 bg-clip-text text-transparent drop-shadow-lg">
        Partial & Unofficial Results
    </h2>
    
    {{-- Real-time Clock Card --}}
    <div class="inline-block mt-2 bg-black/30 backdrop-blur-sm rounded-2xl px-5 py-2 border border-white/10 shadow-md">
        <p class="text-sm md:text-base font-medium text-white/90">
            <span class="opacity-75 mr-1">As of now:</span>
            <span class="font-mono font-bold text-yellow-300" x-text="currentTime"></span>
        </p>
    </div>

    {{-- ================= MODERN NOTE ================= --}}
    <div class="mt-5 max-w-2xl mx-auto text-center">
        <div class="bg-gradient-to-r from-amber-500/20 via-yellow-500/20 to-amber-500/20 backdrop-blur-sm rounded-2xl px-5 py-3 border border-yellow-400/30 shadow-sm transition-all duration-300 hover:border-yellow-400/60 hover:shadow-md">
            <div class="flex items-center justify-center gap-2 mb-1">
                <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                <p class="font-bold text-xs uppercase tracking-wider text-yellow-200">Important Note</p>
            </div>
            <p class="text-xs md:text-sm text-white/90 font-medium">
                Medal tally is based on the official reports submitted by the Tournament Secretary and validated by the Tournament Director.
            </p>
        </div>
    </div>
</div>



            {{-- Tabs --}}
<div class="flex gap-4 mb-6 text-white font-semibold">
    <button
        @click="switchTab('regular')"
        class="px-5 py-2 rounded-lg transition-all duration-200 border"
        :class="activeTab === 'regular'
            ? 'bg-black/60 border-white shadow-lg scale-105'
            : 'bg-black/20 border-transparent hover:bg-black/30'"
    >
        Regular Games
        <div
            x-show="activeTab === 'regular'"
            x-transition
            class="h-1 mt-1 bg-yellow-400 rounded-full"
        ></div>
    </button>

    <button
        @click="switchTab('para')"
        class="px-5 py-2 rounded-lg transition-all duration-200 border"
        :class="activeTab === 'para'
            ? 'bg-black/60 border-white shadow-lg scale-105'
            : 'bg-black/20 border-transparent hover:bg-black/30'"
    >
        Para Games
        <div
            x-show="activeTab === 'para'"
            x-transition
            class="h-1 mt-1 bg-yellow-400 rounded-full"
        ></div>
    </button>
</div>


            {{-- ================= MEDAL TABLE ================= --}}
<div class="flex-1 overflow-y-auto pr-2">

    {{-- ================= REGULAR TABLE ================= --}}
    <template x-if="activeTab === 'regular'">
        <div class="bg-white/95 rounded-xl overflow-hidden shadow-lg">

            {{-- HEADER --}}
            <div class="grid grid-cols-4 bg-black text-white text-sm font-bold">
                <div class="p-3 text-left">Division (SDO)</div>

                <div class="p-3 text-center border-l">Elementary
                    <div class="grid grid-cols-3 text-xs mt-1 font-semibold">
                        <span>G</span><span>S</span><span>B</span>
                    </div>
                </div>

                <div class="p-3 text-center border-l">Secondary
                    <div class="grid grid-cols-3 text-xs mt-1 font-semibold">
                        <span>G</span><span>S</span><span>B</span>
                    </div>
                </div>

                <div class="p-3 text-center border-l">Overall
                    <div class="grid grid-cols-3 text-xs mt-1 font-semibold">
                        <span>G</span><span>S</span><span>B</span>
                    </div>
                </div>
            </div>

            {{-- BODY --}}
            @foreach($divisions as $division)

            @php
                $all = \App\Models\AllAwardResult::with(['sport','sportType'])
                    ->where('status','approved')
                    ->where('sdo',$division->name)
                    ->get();

                // ✅ FORCE MAP (THIS IS THE FIX)
                $mapped = $all->map(fn($r)=>[
                    'id'=>$r->id,
                    'sport'=>$r->sport,
                    'sportType'=>$r->sportType,
                    'type_name'=>$r->sportType->type_name ?? null, // ✅ ADD THIS
                    'type_id'=>$r->type_id,
                    'division'=>$r->division,
                    'category'=>$r->category,
                    'created_at'=>$r->created_at,
                    'medal'=>$r->medal,
                ]);

                $elem = $mapped->where('division','Elementary');
                $sec  = $mapped->where('division','Secondary');

                $elemGold   = $elem->where('medal','Gold')->count();
                $elemSilver = $elem->where('medal','Silver')->count();
                $elemBronze = $elem->where('medal','Bronze')->count();

                $secGold   = $sec->where('medal','Gold')->count();
                $secSilver = $sec->where('medal','Silver')->count();
                $secBronze = $sec->where('medal','Bronze')->count();

                $overall = $mapped->whereIn('division',['Elementary','Secondary']);
            @endphp

            <div class="grid grid-cols-4 border-t">

                {{-- SDO --}}
                <div class="p-3 font-semibold flex items-center gap-2">
                    @if($division->logo)
                        <img src="{{ asset('storage/'.$division->logo) }}" class="w-6 h-6 object-contain">
                    @endif
                    {{ $division->name }}
                </div>

                {{-- ELEMENTARY --}}
                <div 
                    class="grid grid-cols-3 text-center border-l p-3 cursor-pointer hover:bg-yellow-50"
                    @click="openModal(@js($division), @js($elem->values()))"
                >
                    <span class="font-bold text-yellow-600">{{ $elemGold }}</span>
                    <span class="font-bold text-gray-600">{{ $elemSilver }}</span>
                    <span class="font-bold text-orange-600">{{ $elemBronze }}</span>
                </div>

                {{-- SECONDARY --}}
                <div 
                    class="grid grid-cols-3 text-center border-l p-3 cursor-pointer hover:bg-yellow-50"
                    @click="openModal(@js($division), @js($sec->values()))"
                >
                    <span class="font-bold text-yellow-600">{{ $secGold }}</span>
                    <span class="font-bold text-gray-600">{{ $secSilver }}</span>
                    <span class="font-bold text-orange-600">{{ $secBronze }}</span>
                </div>

                {{-- OVERALL --}}
                <div 
                    class="grid grid-cols-3 text-center border-l p-3 bg-gray-50 cursor-pointer hover:bg-gray-100"
                    @click="openModal(@js($division), @js($overall->values()))"
                >
                    <span class="font-bold text-yellow-700">{{ $elemGold + $secGold }}</span>
                    <span class="font-bold text-gray-700">{{ $elemSilver + $secSilver }}</span>
                    <span class="font-bold text-orange-700">{{ $elemBronze + $secBronze }}</span>
                </div>

            </div>

            @endforeach

        </div>
    </template>

    {{-- ================= PARAGAMES TABLE ================= --}}
    <template x-if="activeTab === 'para'">
        <div class="bg-white/95 rounded-xl overflow-hidden shadow-lg">

            {{-- HEADER --}}
            <div class="grid grid-cols-3 bg-purple-700 text-white text-sm font-bold">
                <div class="p-3 text-left">Division (SDO)</div>

                <div class="p-3 text-center border-l">Paragames
                    <div class="grid grid-cols-3 text-xs mt-1 font-semibold">
                        <span>G</span><span>S</span><span>B</span>
                    </div>
                </div>

                <div class="p-3 text-center border-l">Total</div>
            </div>

            @foreach($divisions as $division)

            @php
                $paraRaw = \App\Models\AllAwardResult::with(['sport','sportType'])
                    ->where('status','approved')
                    ->where('sdo',$division->name)
                    ->where('division','Paragames')
                    ->get();

                $para = $paraRaw->map(fn($r)=>[
                    'id'=>$r->id,
                    'sport'=>$r->sport,
                    'sportType'=>$r->sportType,
                    'type_name'=>$r->sportType->type_name ?? null,
                    'type_id'=>$r->type_id,
                    'division'=>$r->division,
                    'category'=>$r->category,
                    'created_at'=>$r->created_at,
                    'medal'=>$r->medal,
                ]);

                $gold   = $para->where('medal','Gold')->count();
                $silver = $para->where('medal','Silver')->count();
                $bronze = $para->where('medal','Bronze')->count();
            @endphp

            <div class="grid grid-cols-3 border-t">

                <div class="p-3 font-semibold flex items-center gap-2">
                    @if($division->logo)
                        <img src="{{ asset('storage/'.$division->logo) }}" class="w-6 h-6 object-contain">
                    @endif
                    {{ $division->name }}
                </div>

                <div 
                    class="grid grid-cols-3 text-center border-l p-3 cursor-pointer hover:bg-purple-50"
                    @click="openModal(@js($division), @js($para->values()))"
                >
                    <span class="font-bold text-yellow-600">{{ $gold }}</span>
                    <span class="font-bold text-gray-600">{{ $silver }}</span>
                    <span class="font-bold text-orange-600">{{ $bronze }}</span>
                </div>

                <div 
                    class="grid grid-cols-3 text-center border-l p-3 bg-gray-50 cursor-pointer hover:bg-gray-100"
                    @click="openModal(@js($division), @js($para->values()))"
                >
                    <span class="font-bold text-yellow-700">{{ $gold }}</span>
                    <span class="font-bold text-gray-700">{{ $silver }}</span>
                    <span class="font-bold text-orange-700">{{ $bronze }}</span>
                </div>

            </div>

            @endforeach

        </div>
    </template>

</div>
        </div>
    </div>
</div>

{{-- ================= TV NEWS STYLE MODAL (FULLSCREEN FIXED - Z INDEX FIX) ================= --}}
<div
    x-show="open"
    x-transition.opacity
    @keydown.escape.window="open = false"
    class="fixed inset-0 bg-black/60 z-[200] flex flex-col"
>

    <div
        @click.away="open = false"
        class="w-full h-full flex flex-col bg-white text-gray-800"
    >

        {{-- 🔴 TOP BAR --}}
        <div class="bg-red-600 flex items-center justify-between px-5 py-3 text-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <span class="bg-yellow-400 text-black px-3 py-1 text-xs font-extrabold tracking-widest rounded">
                    eSRRS
                </span>
                <h3
                    class="text-sm md:text-xl font-extrabold uppercase tracking-wide truncate"
                    x-text="division?.name + ' — Medal Update'"
                ></h3>
            </div>

            <button
                @click="open = false"
                class="text-white text-3xl font-bold hover:text-gray-200 leading-none"
            >
                &times;
            </button>
        </div>

        {{-- 📺 CONTENT --}}
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 overflow-y-auto">

            {{-- GOLD --}}
            <div class="flex flex-col bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-yellow-400 text-black font-bold text-center py-2">
                    GOLD ( <span x-text="medals.gold.length"></span> )
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-3">
                    <template x-for="m in medals.gold" :key="m.id">
                        <div class="border-l-4 border-yellow-500 p-3 rounded bg-white hover:bg-gray-100">
                            <div class="font-bold text-red-600 text-sm">
                                <span x-text="m.sport?.name ?? 'Sport'"></span>
                                <span class="text-gray-500 font-semibold ml-1">
                                    —
                                    <span x-text="m.sportType && m.sportType.type_name ? m.sportType.type_name : ('Type #' + m.type_id)"></span>
                                </span>
                            </div>

                            <div class="text-xs text-gray-600 mt-1">
                                <div>Level: <strong x-text="m.division"></strong></div>
                                <div>Category: <strong x-text="m.category"></strong></div>
                                <div>
                                    <strong x-text="new Date(m.created_at).toLocaleDateString()"></strong>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- SILVER --}}
            <div class="flex flex-col bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gray-300 text-black font-bold text-center py-2">
                    SILVER ( <span x-text="medals.silver.length"></span> )
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-3">
                    <template x-for="m in medals.silver" :key="m.id">
                        <div class="border-l-4 border-gray-400 p-3 rounded bg-white hover:bg-gray-100">
                            <div class="font-bold text-red-600 text-sm">
                                <span x-text="m.sport?.name ?? 'Sport'"></span>
                                <span class="text-gray-500 font-semibold ml-1">
                                    —
                                    <span x-text="m.sportType && m.sportType.type_name ? m.sportType.type_name : ('Type #' + m.type_id)"></span>
                                </span>
                            </div>

                            <div class="text-xs text-gray-600 mt-1">
                                <div>Level: <strong x-text="m.division"></strong></div>
                                <div>Category: <strong x-text="m.category"></strong></div>
                                <div>
                                    <strong x-text="new Date(m.created_at).toLocaleDateString()"></strong>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- BRONZE --}}
            <div class="flex flex-col bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-orange-400 text-black font-bold text-center py-2">
                    BRONZE ( <span x-text="medals.bronze.length"></span> )
                </div>

                <div class="flex-1 overflow-y-auto p-3 space-y-3">
                    <template x-for="m in medals.bronze" :key="m.id">
                        <div class="border-l-4 border-orange-500 p-3 rounded bg-white hover:bg-gray-100">
                            <div class="font-bold text-red-600 text-sm">
                                <span x-text="m.sport?.name ?? 'Sport'"></span>
                                <span class="text-gray-500 font-semibold ml-1">
                                    —
                                    <span x-text="m.sportType && m.sportType.type_name ? m.sportType.type_name : ('Type #' + m.type_id)"></span>
                                </span>
                            </div>

                            <div class="text-xs text-gray-600 mt-1">
                                <div>Level: <strong x-text="m.division"></strong></div>
                                <div>Category: <strong x-text="m.category"></strong></div>
                                <div>
                                    <strong x-text="new Date(m.created_at).toLocaleDateString()"></strong>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="bg-gray-100 text-gray-600 text-xs px-4 py-2 flex justify-between items-center border-t flex-shrink-0">
            <span class="font-semibold">eSRRS • Official Medal Records</span>
            <span class="italic">Press ESC or click outside to close</span>
        </div>

    </div>
</div>
</section>
{{-- ================= END DASHBOARD STRIP ================= --}}


{{-- ================= BOTTOM STACK: NEWS + EVENTS ================= --}}
<section class="py-10 bg-gray-100 px-4 space-y-6">

    {{-- ================= LATEST NEWS & ANNOUNCEMENTS ================= --}}
    <section
        x-data="{
            search: '',
            allAnnouncements: {{ $allAnnouncements->toJson() }},
            latestAnnouncements: {{ $latestAnnouncements->toJson() }},
        }"
    >
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">

            <h2 class="text-xl font-bold text-gray-900 mb-4">
                Latest News & Announcements
            </h2>

            {{-- Search --}}
            <input
                type="text"
                placeholder="Search announcements..."
                x-model="search"
                class="mb-4 w-full px-4 py-2 border rounded-xl text-sm"
            >

            {{-- List --}}
            <div class="space-y-3 max-h-[300px] overflow-y-auto">
                <template
                    x-for="announcement in (
                        search
                            ? allAnnouncements.filter(a =>
                                a.title.toLowerCase().includes(search.toLowerCase()) ||
                                a.content.toLowerCase().includes(search.toLowerCase())
                              )
                            : latestAnnouncements
                    ).slice(0, 5)"
                    :key="announcement.id"
                >
                    <a
                        :href="'{{ route('viewer.announcements') }}#announcement-' + announcement.id"
                        class="block p-4 border rounded-xl hover:bg-gray-50"
                    >
                        <h4 class="font-semibold text-gray-800"
                            x-text="announcement.title"></h4>

                        <p class="text-sm text-gray-600 mt-1"
                           x-text="announcement.content.substring(0,100) + '...'"></p>
                    </a>
                </template>
            </div>

            <a href="{{ route('viewer.announcements') }}"
               class="mt-4 text-sm text-blue-600 hover:underline">
                View all announcements →
            </a>
        </div>
    </section>

    {{-- ================= UPCOMING EVENTS ================= --}}
    <section
        x-data="{
            eventSearch: '',
            allEvents: {{ $allUpcomingEvents->toJson() }},
            latestEvents: {{ $latestUpcomingEvents->toJson() }},

            get filteredEvents() {
                if (!this.eventSearch) return this.latestEvents.slice(0, 5);
                return this.allEvents.filter(e =>
                    e.name?.toLowerCase().includes(this.eventSearch.toLowerCase()) ||
                    e.sport?.name?.toLowerCase().includes(this.eventSearch.toLowerCase())
                ).slice(0, 5);
            }
        }"
    >
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">

            <h2 class="text-xl font-bold text-gray-900 mb-4">
                Upcoming Events
            </h2>

            {{-- Search --}}
            <input
                type="text"
                placeholder="Search events..."
                x-model="eventSearch"
                class="mb-4 w-full px-4 py-2 border rounded-xl text-sm"
            >

            {{-- List --}}
            <div class="space-y-3 max-h-[300px] overflow-y-auto">
                <template x-for="event in filteredEvents" :key="event.id">
                    <a
                        :href="'{{ route('viewer.events') }}#event-' + event.id"
                        class="block p-4 border rounded-xl hover:bg-gray-50"
                    >
                        <h4 class="font-semibold text-gray-800"
                            x-text="event.name"></h4>

                        <p class="text-xs text-gray-500 mt-1"
                           x-text="event.sport?.name ?? 'TBA'"></p>
                    </a>
                </template>
            </div>

            <a href="{{ route('viewer.events') }}"
               class="mt-4 text-sm text-blue-600 hover:underline">
                View all events →
            </a>
        </div>
    </section>

</section>
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
