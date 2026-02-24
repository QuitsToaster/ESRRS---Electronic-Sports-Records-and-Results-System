@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="bg-black min-h-screen text-white">

    <div class="max-w-7xl mx-auto px-4 py-12 space-y-12">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Poppins', sans-serif;">Don't miss the action.</h1>
                <p class="text-gray-400 mt-1" style="font-family: 'Poppins', sans-serif;">Events. Competitions. Champions.</p>
            </div>

            {{-- Search & Status Filter --}}
            <form method="GET" action="{{ route('viewer.events') }}" class="flex gap-2 flex-wrap items-center">

                {{-- Search Input --}}
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search events..."
                       class="border border-gray-700 rounded px-4 py-2 bg-black text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                {{-- Status Filter --}}
                <select name="status"
                        class="border border-gray-700 rounded px-4 py-2 bg-black text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="Upcoming" {{ request('status') === 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="Ongoing" {{ request('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="Finished" {{ request('status') === 'Finished' ? 'selected' : '' }}>Finished</option>
                    <option value="Delayed" {{ request('status') === 'Delayed' ? 'selected' : '' }}>Delayed</option>
                </select>

                {{-- Submit --}}
                <button type="submit"
                        class="bg-red-600 px-4 py-2 rounded font-semibold hover:bg-red-700 transition">
                    Filter
                </button>

                {{-- Clear --}}
                @if(request('search') || request('status'))
                    <a href="{{ route('viewer.events') }}"
                       class="text-gray-400 hover:text-white px-2 py-2 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Events Grid --}}
        @if($events->isEmpty())
            <div class="text-gray-400 text-center py-20">No events available at the moment.</div>
        @else
            <div x-data="{ activeEvent: null }" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <div class="bg-gray-900 rounded-2xl shadow-md hover:shadow-2xl transition transform hover:-translate-y-1 p-6 flex flex-col justify-between relative group cursor-pointer border border-gray-800">

                        {{-- Event Info --}}
                        <div>
                            <h3 class="text-2xl font-bold text-center mb-4">
                                {{ $event->name }}
                            </h3>

                            <div class="flex flex-wrap gap-2 mb-2 justify-center">
                                <span class="bg-blue-800 px-2 py-1 rounded text-xs">{{ $event->sport->name ?? '-' }}</span>
                                <span class="bg-green-800 px-2 py-1 rounded text-xs">{{ $event->age_group ?? '-' }}</span>
                                <span class="bg-purple-800 px-2 py-1 rounded text-xs">{{ $event->category ?? '-' }}</span>
                            </div>

                            <p class="text-gray-400 text-sm text-center">
                                <b>Date:</b> {{ $event->start_date?->format('M d, Y') ?? 'TBA' }}
                            </p>
                            <p class="text-gray-400 text-sm text-center">
                                <b>Venue:</b> {{ $event->venue ?? 'TBA' }}
                            </p>

                            {{-- Status --}}
                            @if($event->status)
                                @php
                                    $statusColor = match($event->status) {
                                        'Upcoming' => 'bg-green-600',
                                        'Ongoing'  => 'bg-blue-600',
                                        'Finished' => 'bg-gray-600',
                                        'Delayed'  => 'bg-red-600',
                                        default    => 'bg-gray-700',
                                    };
                                @endphp
                                <p class="text-center mt-2">
                                    <span class="{{ $statusColor }} text-white px-3 py-1 rounded text-sm font-semibold">
                                        {{ ucfirst($event->status) }}
                                    </span>

                                    @if(strtolower($event->status) === 'delayed' && $event->status_reason)
                                        <span class="text-red-400 italic text-xs block mt-1">
                                            <b>Reason:</b> {{ $event->status_reason }}
                                        </span>
                                    @endif
                                </p>
                            @endif
                        </div>

                        {{-- Action --}}
                        <button @click="activeEvent = {{ $event->id }}"
                                class="mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 rounded font-semibold w-full">
                            View Details
                        </button>

                        <div class="absolute inset-0 bg-blue-50/10 opacity-0 group-hover:opacity-30 transition rounded-2xl pointer-events-none"></div>
                    </div>
                @endforeach

                {{-- Event Modal --}}
                <template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
                    <div x-show="activeEvent === event.id"
                         x-transition.opacity
                         class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center p-4 overflow-auto"
                         style="display: none;">
                        <div @click.away="activeEvent = null"
                             class="bg-gray-900 w-full md:max-w-6xl rounded-2xl shadow-2xl overflow-hidden">

                            {{-- Header --}}
                            <div class="flex justify-between items-center p-6 border-b border-gray-700">
                                <h3 class="text-3xl font-bold text-white" x-text="event.name"></h3>
                                <button @click="activeEvent = null"
                                        class="text-gray-400 hover:text-white text-3xl">&times;</button>
                            </div>

                            {{-- Body --}}
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-200">

                                {{-- Left --}}
                                <div class="space-y-6">
                                    <div class="grid grid-cols-2 gap-4 bg-gray-800 p-4 rounded-xl shadow">
                                        <div class="font-semibold">Sport:</div><div x-text="event.sport?.name ?? '-'"></div>
                                        <div class="font-semibold">Division:</div><div x-text="event.age_group ?? '-'"></div>
                                        <div class="font-semibold">Category:</div><div x-text="event.category ?? '-'"></div>
                                        <div class="font-semibold">Venue:</div><div x-text="event.venue ?? '-'"></div>
                                        <div class="font-semibold">Date:</div>
                                        <div x-text="event.start_date ? new Date(event.start_date).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : 'TBA'"></div>
                                    </div>

                                    <div class="bg-gray-800 p-4 rounded-xl shadow">
                                        <h4 class="text-xl font-semibold mb-4">Matches</h4>
                                        <template x-for="(game, index) in event.games ?? []" :key="index">
                                            <div class="bg-gray-700 px-3 py-2 rounded mb-2">
                                                <b x-text="'Game ' + (index + 1)"></b>: <span x-text="game"></span>
                                            </div>
                                        </template>
                                        <div x-show="!event.games?.length" class="text-gray-400 italic">
                                            No games assigned yet.
                                        </div>
                                    </div>
                                </div>

                                {{-- Right --}}
                                <div class="space-y-6">
                                    <div class="bg-gray-800 p-4 rounded-xl shadow">
                                        <h4 class="text-xl font-semibold mb-4">Participating SDOs</h4>
                                        <template x-for="division in event.divisions" :key="division.id">
                                            <div class="bg-gray-700 px-3 py-2 rounded mb-2" x-text="division.name"></div>
                                        </template>
                                    </div>

                                    <div class="bg-gray-800 p-4 rounded-xl shadow">
                                        <h4 class="text-xl font-semibold mb-4">Winners</h4>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="bg-yellow-700 p-4 rounded text-center">Gold<br><b x-text="event.goldWinner?.name ?? '-'"></b></div>
                                            <div class="bg-gray-600 p-4 rounded text-center">Silver<br><b x-text="event.silverWinner?.name ?? '-'"></b></div>
                                            <div class="bg-orange-700 p-4 rounded text-center">Bronze<br><b x-text="event.bronzeWinner?.name ?? '-'"></b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="p-5 border-t border-gray-700 text-right">
                                <button @click="activeEvent = null"
                                        class="px-5 py-2 bg-gray-700 hover:bg-gray-600 rounded">
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

<script src="//unpkg.com/alpinejs" defer></script>
@endsection