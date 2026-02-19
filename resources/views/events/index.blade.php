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
        <li>Manage matches and winners by clicking <strong>Manage</strong> in the Actions column.</li>
        <li>Edit or delete competitions directly from the Actions column as needed.</li>
    </ul>
</div>

<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of CAVRAA competitions registered in the system
        </p>
    </div>

    <a href="{{ route('events.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        + Add Competition
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
           placeholder="Search competition, sport, division, venue..."
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
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Venue</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Winners</th>
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



                    {{-- Venue --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $event->venue ?? 'TBA' }}
                    </td>

                    {{-- Winners --}}
                    <td class="px-4 py-3 text-sm">
                        @if($event->goldWinner || $event->silverWinner || $event->bronzeWinner)
                            <div class="flex flex-col gap-1">
                                @if($event->goldWinner)
                                    <span class="text-yellow-500 font-semibold">🥇 {{ $event->goldWinner->name }}</span>
                                @endif
                                @if($event->silverWinner)
                                    <span class="text-gray-500 font-semibold">🥈 {{ $event->silverWinner->name }}</span>
                                @endif
                                @if($event->bronzeWinner)
                                    <span class="text-orange-500 font-semibold">🥉 {{ $event->bronzeWinner->name }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400 italic">No winners yet</span>
                        @endif
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
