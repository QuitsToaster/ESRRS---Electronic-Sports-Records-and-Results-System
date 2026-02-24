@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="bg-black min-h-screen text-white">

    <div class="max-w-7xl mx-auto px-4 py-12 space-y-12">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold font-[Poppins]">
                    Don't miss the action.
                </h1>
                <p class="text-gray-400 mt-1 font-[Poppins]">
                    Events. Competitions. Champions.
                </p>
            </div>

            {{-- Search & Filter --}}
            <form method="GET" action="{{ route('viewer.events') }}"
                  class="flex gap-2 flex-wrap items-center">

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search events..."
                       class="border border-gray-700 rounded px-4 py-2 bg-black text-white focus:ring-2 focus:ring-blue-500">

                <select name="status"
                        class="border border-gray-700 rounded px-4 py-2 bg-black text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    @foreach(['Upcoming','Ongoing','Finished','Delayed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>

                <button class="bg-red-600 px-4 py-2 rounded font-semibold hover:bg-red-700">
                    Filter
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('viewer.events') }}"
                       class="text-gray-400 hover:text-white px-2 py-2">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Events Grid --}}
        @if($events->isEmpty())
            <div class="text-gray-400 text-center py-20">
                No events available at the moment.
            </div>
        @else
            <div x-data="{ 
        activeEvent: {{ request('event') ?? 'null' }} 
    }" 
    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    @foreach($events as $event)
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-md hover:shadow-2xl transition hover:-translate-y-1">
            <h3 class="text-2xl font-bold text-center mb-4">{{ $event->name }}</h3>

            <div class="flex flex-wrap gap-2 justify-center mb-3">
                <span class="bg-blue-700 px-2 py-1 rounded text-xs">{{ $event->sport->name ?? '-' }}</span>
                <span class="bg-green-700 px-2 py-1 rounded text-xs">{{ $event->age_group }}</span>
                <span class="bg-purple-700 px-2 py-1 rounded text-xs">{{ $event->category }}</span>
            </div>

            <p class="text-sm text-center text-gray-400 mb-2">
                <b>Date:</b> {{ $event->start_date?->format('M d, Y') ?? 'TBA' }}
            </p>
            <p class="text-sm text-center text-gray-400 mb-2">
                <b>Venue:</b> {{ $event->venue ?? 'TBA' }}
            </p>

            {{-- Event Status --}}
            @php
                $statusColor = match($event->status) {
                    'Upcoming' => 'bg-green-600',
                    'Ongoing'  => 'bg-blue-600',
                    'Finished' => 'bg-gray-600',
                    'Delayed'  => 'bg-red-600',
                    default    => 'bg-gray-700',
                };
            @endphp
            <p class="text-center mb-4">
                <span class="{{ $statusColor }} text-white px-3 py-1 rounded text-sm font-semibold">
                    {{ ucfirst($event->status) }}
                </span>
                @if(strtolower($event->status) === 'delayed' && $event->status_reason)
                    <span class="text-red-400 italic text-xs block mt-1">
                        <b>Reason:</b> {{ $event->status_reason }}
                    </span>
                @endif
            </p>

            <div class="text-center">
                <button @click="activeEvent = {{ $event->id }}"
                        class="mt-2 w-40 bg-red-600 hover:bg-red-700 py-2 rounded font-semibold transition">
                    View Details
                </button>
            </div>
        </div>
    @endforeach

    {{-- EVENT MODALS --}}
    <template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
        <div x-show="activeEvent === event.id"
             x-transition.opacity
             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display:none">

            <div @click.away="activeEvent = null" class="bg-gray-900 w-full max-w-4xl rounded-3xl overflow-hidden shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex justify-between items-center p-6 border-b border-gray-700">
                    <div>
                        <h3 class="text-3xl font-bold" x-text="event.name"></h3>
                        <p class="text-gray-400 text-sm mt-1" x-text="event.category + ' • ' + event.age_group"></p>
                    </div>
                    <button @click="activeEvent = null" class="text-gray-400 hover:text-white text-3xl">&times;</button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 grid grid-cols-1 gap-6">
                    {{-- Event Info --}}
                    <div class="bg-gray-800 rounded-xl p-5 grid grid-cols-2 gap-4 text-sm">
                        <div class="font-semibold">Sport</div>
                        <div x-text="event.sport?.name ?? '-'"></div>

                        <div class="font-semibold">Venue</div>
                        <div x-text="event.venue ?? 'TBA'"></div>

                        <div class="font-semibold">Date</div>
                        <div x-text="event.start_date
                            ? new Date(event.start_date).toLocaleDateString('en-US',
                              {month:'short',day:'numeric',year:'numeric'})
                            : 'TBA'"></div>
                    </div>

                    {{-- Games & Winners --}}
                    <div class="bg-gray-800 rounded-xl p-5">
                        <h4 class="text-xl font-semibold mb-4">Matches & Winners</h4>

                        <template x-for="(game, index) in event.games" :key="index">
                            <div class="mb-3 bg-gray-700 rounded-lg p-3 transition hover:bg-gray-600 cursor-pointer">
                                <div class="font-semibold text-sm mb-1">Game <span x-text="index + 1"></span></div>
                                <div class="text-gray-300 text-sm mb-2" x-text="game"></div>
                                <div>
                                    <span class="text-xs uppercase text-gray-400">Winner</span>
                                    <div class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-semibold"
                                         :class="event.game_winners[index] ? 'bg-green-600' : 'bg-gray-600'"
                                         x-text="event.game_winners[index] ?? 'TBA'">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="!event.games.length" class="italic text-gray-400">
                            No games assigned yet.
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-5 border-t border-gray-700 text-right">
                    <button @click="activeEvent = null"
                            class="px-6 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
    // Automatically open modal if `?event=ID` is present
    document.addEventListener('alpine:init', () => {
        Alpine.data('eventsPage', () => ({
            activeEvent: null,
            init() {
                const params = new URLSearchParams(window.location.search);
                const eventId = params.get('event');
                if (eventId) this.activeEvent = parseInt(eventId);
            }
        }));
    });
</script>
        @endif
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection