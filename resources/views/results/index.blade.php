@extends('layouts.app')

@section('page-title', 'Results / Awards')

@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-600 mt-1">
        List of Awarded Medals
    </p>
    <div class="flex gap-2">
        <a href="{{ route('results.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Award Medal
        </a>

        <a href="{{ route('results.export') }}" 
           class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded" target="_blank">
            Export Awards (PDF)
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Search Bar --}}
<form method="GET" action="{{ route('results.index') }}" class="mb-6 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search event, school, sport, division, medal, athlete, or date..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('results.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    @endif
</form>

{{-- Medal Summary / Tally --}}
<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-3">Medal Summary</h2>
    @if($medalTally->isEmpty())
        <div class="text-gray-500 text-center py-6">No medals awarded yet.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($medalTally as $school)
                <div class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm p-4 hover:shadow-md transition">
                    {{-- School Logo --}}
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold mr-4">
                            {{ strtoupper(substr($school->name,0,2)) }}
                        </div>
                    @endif

                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold text-gray-800">{{ $school->name }}</span>
                            <span class="text-gray-500 font-medium">Rank: {{ $school->rank }}</span>
                        </div>
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center space-x-1">
                                <span class="w-4 h-4 bg-yellow-400 rounded-full inline-block"></span>
                                <span class="font-semibold">{{ $school->gold }} Gold</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-4 h-4 bg-gray-400 rounded-full inline-block"></span>
                                <span class="font-semibold">{{ $school->silver }} Silver</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-4 h-4 bg-orange-500 rounded-full inline-block"></span>
                                <span class="font-semibold">{{ $school->bronze }} Bronze</span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 mt-1">Total Points: {{ $school->points }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Results Table by Division --}}
@foreach(['Elementary','Secondary'] as $division)
    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $division }} Division</h2>

    @php
        $divisionResults = $results->where('division', $division);
    @endphp

    @if($divisionResults->isEmpty())
        <div class="text-gray-500 py-4">No results recorded for {{ $division }} Division.</div>
    @else
        <div class="bg-white rounded shadow overflow-x-auto mb-6">
            <table class="w-full text-left table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Event</th>
                        <th class="px-4 py-2">Sport</th>
                        <th class="px-4 py-2">Team</th>
                        <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Medal</th>
                        <th class="px-4 py-2">Awarded By</th>
                        <th class="px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($divisionResults as $result)
                        <tr class="border-b">
                          
<td class="px-4 py-2">{{ $result->event->name ?? '-' }}</td>
<td class="px-4 py-2">{{ $result->sport->name ?? '-' }}</td>
<td class="px-4 py-2">{{ $result->school->name ?? '-' }}</td>
<td class="px-4 py-2">{{ $result->category }}</td>
<td class="px-4 py-2">
    <span class="@if($result->medal==='Gold') text-yellow-500 font-bold
                  @elseif($result->medal==='Silver') text-gray-400 font-bold
                  @else text-orange-500 font-bold @endif">
        {{ $result->medal }}
    </span>
</td>
<td class="px-4 py-2">{{ $result->awardedBy->name ?? '-' }}</td>
<td class="px-4 py-2">
    {{ 
        $result->created_at 
            ? $result->created_at->format('M d, Y') 
            : ($result->event?->award_date?->format('M d, Y') ?? '-') 
    }}
</td>


                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach
@endsection
