@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="bg-black min-h-screen text-white">

    <div class="max-w-7xl mx-auto px-4 py-12 space-y-12">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold">Don't miss the action.</h1>
                <p class="text-gray-400 mt-1">Events. Competitions. Champions.</p>
                <p class="text-gray-400 mt-1">View Event Schedule and Venue here.</p>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('viewer.events') }}" class="flex gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') ?? '' }}"
                       placeholder="Search events..."
                       class="border border-gray-700 rounded px-4 py-2 bg-black text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-red-600 px-4 py-2 rounded font-semibold hover:bg-red-700 transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('viewer.events') }}" class="text-gray-400 hover:text-white px-2 py-2 transition">
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

                            {{-- Status Label --}}
                            @if($event->status)
                                @php
                                    $statusColor = match($event->status) {
                                        'Upcoming' => 'bg-green-600',
                                        'Ongoing'   => 'bg-blue-600',
                                        'Finished'  => 'bg-gray-600',
                                        'Delayed'   => 'bg-red-600',
                                        default     => 'bg-gray-700',
                                    };
                                @endphp
                                <p class="text-center mt-2">
                                    <span class="{{ $statusColor }} text-white px-3 py-1 rounded text-sm font-semibold">
                                        {{ ucfirst($event->status) }}
                                    </span>

                                    {{-- Show status_reason only if Delayed --}}
                                    @if(strtolower($event->status) === 'delayed' && $event->status_reason)
                                        <span class="text-red-400 italic text-xs block mt-1"><span class="text-red-400 italic font-bold text-xs block mt-1">Reason:</span>{{ $event->status_reason }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>

                        {{-- Action Button --}}
                        @if($event->is_ball_game && $event->games->first())
                            <a href="{{ route('viewer.game.modal', $event->games->first()->id) }}"
                               class="mt-4 text-center px-4 py-2 bg-green-600 hover:bg-green-700 rounded font-bold">
                                ▶ Watch Live Game
                            </a>
                        @else
                            <button @click="activeEvent = {{ $event->id }}"
                                    class="mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 rounded font-semibold w-full">
                                View Details
                            </button>
                        @endif

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-blue-50/10 opacity-0 group-hover:opacity-30 transition rounded-2xl pointer-events-none"></div>
                    </div>
                @endforeach

                {{-- Event Modal --}}
                <template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
                    <div x-show="activeEvent === event.id" x-transition.opacity
                         class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center p-4 overflow-auto"
                         style="display: none;">
                        <div @click.away="activeEvent = null"
                             class="bg-gray-900 w-full md:max-w-4xl rounded-2xl shadow-2xl overflow-hidden relative">

                            {{-- Modal Header --}}
                            <div class="flex justify-between items-center p-5 border-b border-gray-700">
                                <h3 class="text-3xl font-bold text-white" x-text="event.name"></h3>
                                <button @click="activeEvent = null" class="text-gray-400 hover:text-white text-3xl">&times;</button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="p-6 space-y-6 text-gray-200">

                                {{-- Event Info --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div><span class="font-semibold">Sport:</span> <span x-text="event.sport?.name ?? '-'"></span></div>
                                    <div><span class="font-semibold">Division:</span> <span x-text="event.age_group ?? '-'"></span></div>
                                    <div><span class="font-semibold">Category:</span> <span x-text="event.category ?? '-'"></span></div>
                                    <div><span class="font-semibold">Venue:</span> <span x-text="event.venue ?? '-'"></span></div>
                                    <div>
                                        <span class="font-semibold">Date:</span>
                                        <span x-text="event.start_date ? new Date(event.start_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'TBA'"></span>
                                        <span x-show="event.end_date"> - <span x-text="new Date(event.end_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })"></span></span>
                                    </div>

                                    {{-- Status Reason in Modal (only for Delayed) --}}
                                    <div x-show="event.status.toLowerCase() === 'delayed' && event.status_reason" class="text-red-400 text-sm italic col-span-full">
                                        <span x-text="'Reason: ' + event.status_reason"></span>
                                    </div>
                                </div>

                                {{-- Participating Divisions --}}
                                <div>
                                    <h4 class="text-xl font-semibold text-white mb-3">Participating SDOs</h4>
                                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 list-disc list-inside">
                                        <template x-for="division in event.divisions" :key="division.id">
                                            <li class="text-gray-200 font-medium" x-text="division.name"></li>
                                        </template>
                                        <li x-show="!event.divisions.length" class="text-gray-400 italic">No divisions assigned.</li>
                                    </ul>
                                </div>

                                {{-- Winners --}}
                                <div>
                                    <h4 class="text-xl font-semibold text-white mb-3">Winners</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="bg-yellow-700 text-yellow-100 p-4 rounded-xl text-center shadow">
                                            <div class="font-bold text-lg">Gold</div>
                                            <div class="mt-1 font-medium" x-text="event.goldWinner ? event.goldWinner.name : '-'"></div>
                                        </div>
                                        <div class="bg-gray-800 text-gray-100 p-4 rounded-xl text-center shadow">
                                            <div class="font-bold text-lg">Silver</div>
                                            <div class="mt-1 font-medium" x-text="event.silverWinner ? event.silverWinner.name : '-'"></div>
                                        </div>
                                        <div class="bg-orange-700 text-orange-100 p-4 rounded-xl text-center shadow">
                                            <div class="font-bold text-lg">Bronze</div>
                                            <div class="mt-1 font-medium" x-text="event.bronzeWinner ? event.bronzeWinner.name : '-'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Modal Footer --}}
                            <div class="flex justify-end p-5 border-t border-gray-700">
                                <button @click="activeEvent = null"
                                        class="px-5 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg shadow transition">
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

{{-- Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
