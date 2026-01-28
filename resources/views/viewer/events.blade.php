@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Upcoming & Ongoing Events</h2>
            <p class="text-gray-600 mt-1 text-sm">Browse all scheduled events in the system.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('viewer.events') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                   placeholder="Search events..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('viewer.events') }}" class="text-gray-500 hover:text-gray-700 px-2 py-2 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Events Grid --}}
    @if($events->isEmpty())
        <div class="text-gray-500 text-center py-10">No events available at the moment.</div>
    @else
        <div x-data="{ activeEvent: null }" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition transform hover:-translate-y-1 p-5 flex flex-col justify-between">

                    {{-- Event Card --}}
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $event->name }}</h3>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Date:</span>
                            {{ $event->start_date
                                ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y')
                                : 'TBA'
                            }}
                            @if($event->end_date)
                                - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                            @endif
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Venue:</span> {{ $event->venue ?? 'TBA' }}
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Sport:</span> {{ $event->sport->name ?? '-' }}
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Division:</span> {{ $event->age_group ?? '-' }}
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Category:</span> {{ $event->category ?? '-' }}
                        </p>
                    </div>

                    {{-- View Details Button --}}
                    <button @click="activeEvent = {{ $event->id }}"
                            class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition">
                        View Details
                    </button>
                </div>
            @endforeach

            {{-- Full Page Modal --}}
<template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
    <div x-show="activeEvent === event.id"
         x-transition
         class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4 overflow-auto"
         style="display: none;">
        <div @click.away="activeEvent = null"
             class="bg-white w-full h-full md:h-auto md:max-w-4xl rounded-lg shadow-lg overflow-auto relative">

            {{-- Modal Header --}}
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-2xl font-bold text-gray-800" x-text="event.name"></h3>
                <button @click="activeEvent = null" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-6 text-gray-700">

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
                </div>

                {{-- Participating Schools --}}
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Participating Schools</h4>
                    <ul class="list-disc list-inside space-y-1">
                        <template x-for="school in event.schools" :key="school.id">
                            <li x-text="school.name"></li>
                        </template>
                        <li x-show="!event.schools.length">No schools assigned.</li>
                    </ul>
                </div>

                {{-- Winners --}}
<div>
    <h4 class="text-lg font-semibold text-gray-800 mb-2">Winners</h4>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-yellow-100 p-4 rounded text-center">
            <div class="text-lg font-bold">Gold</div>
            <div class="text-gray-700 mt-1" x-text="event.goldWinner ? event.goldWinner.name : '-'"></div>
        </div>

        <div class="bg-gray-200 p-4 rounded text-center">
            <div class="text-lg font-bold">Silver</div>
            <div class="text-gray-700 mt-1" x-text="event.silverWinner ? event.silverWinner.name : '-'"></div>
        </div>

        <div class="bg-orange-100 p-4 rounded text-center">
            <div class="text-lg font-bold">Bronze</div>
            <div class="text-gray-700 mt-1" x-text="event.bronzeWinner ? event.bronzeWinner.name : '-'"></div>
        </div>

    </div>
</div>


            </div>

            {{-- Modal Footer --}}
            <div class="flex justify-end p-4 border-t">
                <button @click="activeEvent = null" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded shadow transition">
                    Close
                </button>
            </div>

        </div>
    </div>
</template>


        </div>
    @endif
</div>
@endsection
