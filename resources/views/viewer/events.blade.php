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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition transform hover:-translate-y-1 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $event->name }}</h3>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Date:</span> 
                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                            @if($event->end_date)
                                - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                            @endif
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Venue:</span> {{ $event->venue ?? 'TBD' }}
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Division:</span> {{ $event->division ?? 'All' }}
                        </p>
                        <p class="text-gray-500 mb-1">
                            <span class="font-semibold">Category:</span> {{ $event->category ?? 'All' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
