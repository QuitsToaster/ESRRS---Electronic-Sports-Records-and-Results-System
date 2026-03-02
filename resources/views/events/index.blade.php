@extends('layouts.app')

@section('page-title', 'Events')

@section('content')

{{-- Header --}}

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>View all registered competitions and their details including sport, division, venue, and matches.</li>
        <li>Use the search box to quickly find competitions by name, sport, division, or venue.</li>
        <li>Click <strong>+ Add Competition</strong> to register a new event.</li>
        <li>Update the event status using the dropdown: <strong>Upcoming</strong>, <strong>Ongoing</strong>, <strong>Delayed</strong>, or <strong>Finished</strong>. Provide a reason if delayed.</li>
        <li>Manage matches by clicking <strong>Manage</strong> in the Actions column.</li>
        <li>Edit or delete competitions directly from the Actions column as needed.</li>
    </ul>
</div>

{{-- ================= FILTERS & TOTAL COUNT ================= --}}
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4 gap-4">

    <div class="flex flex-wrap items-center gap-3">

        {{-- SEARCH + STATUS FILTER --}}
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-wrap gap-2 items-center">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search competition, sport, venue..."
                   class="border border-gray-300 rounded px-3 py-2 w-64">

            <select name="status" class="border border-gray-300 rounded px-3 py-2">
                <option value="">All Status</option>
                @foreach(['Upcoming','Ongoing','Delayed','Finished'] as $s)
                    <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded shadow">
                Filter
            </button>

            <a href="{{ route('events.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Reset
            </a>

        </form>

        {{-- TOTAL / FILTERED COUNT --}}
<div class="text-sm text-gray-600 ml-2">
    @if(request('status'))
        <span class="font-semibold">
            {{ request('status') }} Events:
        </span>
        {{ $displayCount }}
    @elseif(request('search'))
        <span class="font-semibold">
            Filtered Events:
        </span>
        {{ $displayCount }}
    @else
        <span class="font-semibold">
            Total Events:
        </span>
        {{ $totalEvents }}
    @endif
</div>
    </div>

    {{-- ADD EVENT --}}
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

{{-- Empty State --}}
@if($events->isEmpty())
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No competitions found.
    </div>
@else

{{-- Events Table --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Event</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Event Type</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Level</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Matches</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Game Winners</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Venue</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @foreach($events as $event)
                <tr class="hover:bg-gray-50 transition align-top">
                    {{-- Status --}}
<td class="px-4 py-3 font-semibold" x-data="{
        status: '{{ $event->status }}',
        reason: '{{ $event->status_reason ?? '' }}',
        submitForm() { $refs.statusForm.submit(); }
    }"
>
    <form method="POST" action="{{ route('events.updateStatus', $event->id) }}" x-ref="statusForm">
        @csrf

        {{-- Status Dropdown --}}
        <select name="status" x-model="status" @change="submitForm()"
                class="px-3 py-1 rounded-full text-sm font-semibold
                    @php
                        echo match($event->status) {
                            'Upcoming' => 'bg-blue-100 text-blue-700',
                            'Ongoing'  => 'bg-green-100 text-green-700',
                            'Delayed'  => 'bg-yellow-100 text-yellow-700',
                            'Finished' => 'bg-gray-200 text-gray-700',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                    @endphp">
            @foreach(['Upcoming','Ongoing','Delayed','Finished'] as $s)
                <option value="{{ $s }}" :selected="status === '{{ $s }}'">{{ $s }}</option>
            @endforeach
        </select>

        {{-- Reason Text Field (only when Delayed) --}}
        <template x-if="status === 'Delayed'">
            <input type="text" name="status_reason" x-model="reason" 
                   placeholder="Enter reason for delay"
                   class="ml-2 px-2 py-1 rounded border border-yellow-300 text-sm"
                   @change="submitForm()">
        </template>
    </form>
</td>


{{-- Start Date --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}
                    </td>


                    {{-- Sport --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->sport->name ?? 'TBA' }}
                    </td>

                    {{-- Competition --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $event->name }}
                    </td>

                    {{-- Level --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->age_group ?? 'TBA' }}
                    </td>

                    {{-- Category --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->category ?? 'TBA' }}
                    </td>

                   {{-- Match --}}
<td class="px-4 py-3 text-gray-700">
    @if(!empty($event->games) && is_array($event->games))
        <ul class="list-none p-0 m-0">
            @foreach($event->games as $game)
                <li><strong>Game {{ $loop->iteration }}:</strong> {{ $game }}</li>
            @endforeach
        </ul>
    @else
        TBA
    @endif
</td>

{{-- Category --}}
                    <td class="px-4 py-3 text-gray-700">
    @if(is_array($event->game_winners) && count($event->game_winners))
        <ul class="list-disc list-inside text-sm">
            @foreach($event->game_winners as $gameIndex => $divisionId)
                @php
                    $division = $event->divisions->firstWhere('id', $divisionId);
                @endphp
                <li class="list-none p-0 m-0" >
                    <strong>Game {{ $gameIndex + 1 }}:</strong>
                    <span>
                        {{ $division?->name ?? 'TBA' }}
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <span class="italic text-gray-400">TBA</span>
    @endif
</td>



                    {{-- Venue --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->venue ?? 'TBA' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-4 text-sm font-medium">
                            <a href="{{ route('events.manage', $event->id) }}"
                               class="text-blue-600 hover:text-blue-800">
                                Manage
                            </a>

                            <a href="{{ route('events.edit', $event->id) }}"
                               class="text-yellow-600 hover:text-yellow-800">
                                Edit
                            </a>

                            <form action="{{ route('events.destroy', $event->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this competition?')">
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
