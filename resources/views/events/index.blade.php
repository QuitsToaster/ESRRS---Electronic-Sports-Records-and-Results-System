@extends('layouts.app')

@section('page-title', 'Events')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of CAVRAA events registered in the system
        </p>
    </div>

    <a href="{{ route('events.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        + Add Event
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Search Bar --}}
<form method="GET" action="{{ route('events.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search events..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('events.index') }}"
           class="text-gray-500 hover:text-gray-700 ml-2">
            Clear
        </a>
    @endif
</form>

{{-- Empty State --}}
@if($events->isEmpty())
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No events found.
    </div>
@else

{{-- Events Table --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Event Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sport</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Level</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Age Group</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">End Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Venue</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @foreach($events as $event)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Event Name --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $event->name }}
                    </td>

                    {{-- Sport --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ optional($event->sport)->name ?? 'TBA' }}
                    </td>

                    {{-- Level --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->level ?? 'TBA' }}
                    </td>

                    {{-- Age Group --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->age_group ?? 'TBA' }}
                    </td>

                    {{-- Category --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->category ?? 'TBA' }}
                    </td>

                    {{-- Start Date --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}
                    </td>

                    {{-- End Date --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->end_date ? $event->end_date->format('M d, Y') : 'TBA' }}
                    </td>

                    {{-- Venue --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->venue ?? 'TBA' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end items-center gap-4 text-sm font-medium">
                            <a href="{{ route('events.edit', $event->id) }}"
                               class="text-yellow-600 hover:text-yellow-800">
                                Edit
                            </a>

                            <form action="{{ route('events.destroy', $event->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
