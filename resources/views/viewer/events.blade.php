@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white">

    <div class="max-w-7xl mx-auto px-6 py-14 space-y-14">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">

            <div>
                <h1 class="text-5xl font-bold tracking-tight" style="font-family: 'Poppins'">
                    Don't miss the action.
                </h1>
                <p class="text-gray-400 mt-3 text-lg" style="font-family: 'Poppins'">
                    Stay updated with competitions, schedules, and results.
                </p>
            </div>

            {{-- SEARCH & FILTER --}}
            <form method="GET" action="{{ route('viewer.events') }}"
                  class="flex flex-wrap gap-3 items-center">

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search events..."
                       class="bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none transition w-56">

                <select name="status"
                        class="bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none transition">
                    <option value="">All Status</option>
                    @foreach(['Upcoming','Ongoing','Finished','Delayed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>

                <button class="bg-red-600 hover:bg-red-700 px-5 py-2.5 rounded-xl font-semibold transition">
                    Apply
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('viewer.events') }}"
                       class="text-gray-400 hover:text-white text-sm">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ================= EVENT COUNT ================= --}}
<div class="grid grid-cols-1 gap-6">

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl">
        <div class="text-sm text-gray-400 uppercase tracking-wide">
            {{ $eventsLabel }}
        </div>

        <div class="mt-2 text-4xl font-bold text-white">
            {{ $eventsCount }}
        </div>

        <div class="mt-1 text-xs text-gray-500">
            {{ $eventsLabel === 'Total Events'
                ? 'All events in the system'
                : 'Based on current filters' }}
        </div>
    </div>

</div>

        {{-- ================= EVENTS GRID ================= --}}
        @if($events->isEmpty())
            <div class="text-center text-gray-500 py-24 text-lg">
                No events available at the moment.
            </div>
        @else

<div x-data="eventsPage()" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

    @foreach($events as $event)

        @php
            $statusColor = match($event->status) {
                'Upcoming' => 'bg-green-500/20 text-green-400',
                'Ongoing'  => 'bg-blue-500/20 text-blue-400',
                'Finished' => 'bg-green-500/20 text-gray-300',
                'Delayed'  => 'bg-red-500/20 text-red-400',
                default    => 'bg-gray-700 text-gray-300',
            };
        @endphp

        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-7 hover:border-red-500/40 transition duration-300 shadow-lg hover:shadow-red-500/10">

            <div class="flex justify-between items-start mb-4">
                <h3 class="text-2xl font-semibold leading-tight group-hover:text-red-400 transition">
                    {{ $event->sport->name ?? '-' }} - {{ $event->name }}
                </h3>

                <span class="px-3 py-1 text-xs rounded-full font-medium {{ $statusColor }}">
                    {{ ucfirst($event->status) }}
                </span>
            </div>

            <div class="flex flex-wrap gap-2 mb-4 text-xs">
                <span class="bg-gray-800 px-3 py-1 rounded-full">
                    {{ $event->age_group }}
                </span>
                <span class="bg-gray-800 px-3 py-1 rounded-full">
                    {{ $event->category }}
                </span>
            </div>

            <div class="space-y-2 text-sm text-gray-400">
                <div>
                    <span class="text-gray-500">Date:</span>
                    {{ $event->start_date?->format('M d, Y') ?? 'TBA' }}
                </div>
                <div>
                    <span class="text-gray-500">Venue:</span>
                    {{ $event->venue ?? 'TBA' }}
                </div>
            </div>

            @if(strtolower($event->status) === 'delayed' && $event->status_reason)
                <div class="mt-3 text-xs text-red-400 italic">
                    Reason: {{ $event->status_reason }}
                </div>
            @endif

            <button
                @click="activeEvent = {{ $event->id }}"
                class="mt-6 w-full bg-red-600 hover:bg-red-700 py-2.5 rounded-xl font-semibold transition">
                View Details
            </button>

        </div>
    @endforeach


    {{-- ================= MODALS ================= --}}
    <template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
        <div x-show="activeEvent === event.id"
             x-transition.opacity
             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
             style="display:none">

            <div @click.away="activeEvent = null"
                 class="bg-gray-950 border border-white/10 w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl">

                {{-- HEADER --}}
                <div class="p-6 border-b border-white/10 flex justify-between items-start">
                    <div>
                        <h3 class="text-3xl font-bold" x-text="event.name"></h3>
                        <p class="text-gray-400 mt-1 text-sm"
                           x-text="event.category + ' • ' + event.age_group">
                        </p>
                    </div>
                    <button @click="activeEvent = null"
                            class="text-gray-500 hover:text-white text-3xl leading-none">
                        &times;
                    </button>
                </div>

                {{-- BODY --}}
                <div class="p-8 space-y-8">

                    {{-- INFO --}}
                    <div class="grid md:grid-cols-3 gap-6 text-sm bg-white/5 rounded-2xl p-6 border border-white/10">
                        <div>
                            <div class="text-gray-400 text-xs uppercase mb-1">Sport</div>
                            <div x-text="event.sport?.name ?? '-'"></div>
                        </div>
                        <div>
                            <div class="text-gray-400 text-xs uppercase mb-1">Venue</div>
                            <div x-text="event.venue ?? 'TBA'"></div>
                        </div>
                        <div>
                            <div class="text-gray-400 text-xs uppercase mb-1">Date</div>
                            <div x-text="event.start_date
                                ? new Date(event.start_date).toLocaleDateString('en-US',
                                  {month:'short',day:'numeric',year:'numeric'})
                                : 'TBA'">
                            </div>
                        </div>
                    </div>

                    {{-- MATCHES --}}
                    <div>
                        <h4 class="text-xl font-semibold mb-5">Matches & Winners</h4>

                        <template x-for="(game, index) in event.games" :key="index">
                            <div class="mb-4 bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                                <div class="text-xs text-gray-400 uppercase mb-1">
                                    Game <span x-text="index + 1"></span>
                                </div>

                                <div class="text-sm mb-3" x-text="game"></div>

                                <div>
                                    <span class="text-xs text-gray-500 uppercase">Winner</span>
                                    <div class="mt-1 px-3 py-1 inline-block rounded-full text-sm font-semibold"
                                         :class="event.game_winners[index]
                                            ? 'bg-green-600/20 text-green-400'
                                            : 'bg-gray-700 text-gray-400'"
                                         x-text="event.game_winners[index] ?? 'TBA'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="!event.games.length" class="italic text-gray-500">
                            No games assigned yet.
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="p-6 border-t border-white/10 text-right">
                    <button @click="activeEvent = null"
                            class="px-6 py-2 bg-gray-800 hover:bg-gray-700 rounded-xl transition">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>

@endif
    </div>
</div>

{{-- ================= ALPINE INIT ================= --}}
<script src="//unpkg.com/alpinejs" defer></script>
<script>
function eventsPage() {
    return {
        activeEvent: null,
        init() {
            const params = new URLSearchParams(window.location.search);
            const eventId = params.get('event');
            if (eventId) {
                this.activeEvent = parseInt(eventId);
            }
        }
    }
}
</script>

@endsection