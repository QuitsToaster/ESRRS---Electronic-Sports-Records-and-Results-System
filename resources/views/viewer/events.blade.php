@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Upcoming & Ongoing Events</h2>
    <p class="text-gray-600 text-sm">Browse all scheduled events in the system.</p>
</div>

@if($events->isEmpty())
    <div class="text-gray-500 text-center py-6">No events available at the moment.</div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-5 flex flex-col justify-between">
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
@endsection
