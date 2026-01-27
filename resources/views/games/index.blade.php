@extends('layouts.app')

@section('page-title', 'Games')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of games registered in the system
        </p>
    </div>
    <a href="{{ route('games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Game
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Search Bar --}}
<form method="GET" action="{{ route('games.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search games..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>
    @if(request('search'))
        <a href="{{ route('games.index') }}" class="text-gray-500 hover:text-gray-700 ml-2">Clear</a>
    @endif
</form>

{{-- Games Table --}}
@if($games->isEmpty())
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No games found.
    </div>
@else
<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto mb-8">

<table class="min-w-full border-collapse">
    <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Event</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Team A</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Team B</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Score (Current Period)</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Time</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Venue</th>
            <th class="px-4 py-2 text-right font-medium text-gray-700">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @foreach($games as $game)
            @php
                $scores = is_array($game->scores) ? $game->scores : (is_string($game->scores) ? json_decode($game->scores, true) : null);
                $scores = $scores ?? ['team_a' => [], 'team_b' => []];
                $status = $game->status ?? 'Upcoming';
                $scoreA = $scores['team_a'][count($scores['team_a'])-1] ?? 0;
                $scoreB = $scores['team_b'][count($scores['team_b'])-1] ?? 0;
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-2 text-gray-800">{{ $game->event->name }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $game->team_a }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $game->team_b }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $scoreA }} - {{ $scoreB }}</td>
                <td class="px-4 py-2 text-gray-800">
                    <span class="
                        @if($status === 'Upcoming') text-blue-600
                        @elseif($status === 'Ongoing') text-green-600
                        @else text-gray-600
                        @endif
                        font-semibold">
                        {{ $status }}
                    </span>
                </td>
                <td class="px-4 py-2 text-gray-800">{{ $game->game_time }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $game->venue }}</td>
                <td class="px-4 py-2 text-right">
                    <a href="{{ route('games.edit', $game->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold mr-2">Edit</a>
                    <a href="{{ route('games.manage', $game->id) }}" 
                       class="text-green-600 hover:text-green-800 font-semibold mr-2">Manage</a>
                    <form action="{{ route('games.destroy', $game->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this game?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif

{{-- Brackets Section --}}
@if($brackets->isNotEmpty())
<h2 class="text-2xl font-bold text-gray-800 mb-4">Tournament Brackets</h2>

<div class="overflow-x-auto">
    @foreach($brackets as $bracket)
        @php
            $matches = json_decode($bracket->structure, true);
        @endphp
        <div class="mb-8">
            <h3 class="font-semibold text-gray-700 mb-2">{{ $bracket->event->name }}</h3>
            <div class="flex space-x-8">
                {{-- Round 1 --}}
                <div class="flex flex-col space-y-4">
                    @foreach($matches as $match)
                        <div class="border border-gray-300 p-2 rounded shadow text-center bg-gray-50">
                            <div>{{ $match['team_a'] }}</div>
                            <div class="text-gray-400">vs</div>
                            <div>{{ $match['team_b'] ?? 'TBD' }}</div>
                        </div>
                    @endforeach
                </div>
                {{-- Future rounds (simplified placeholder) --}}
                <div class="flex flex-col space-y-4 justify-center">
                    <div class="border border-gray-300 p-2 rounded shadow text-center bg-gray-50 w-32">
                        Next Round
                    </div>
                    <div class="border border-gray-300 p-2 rounded shadow text-center bg-gray-50 w-32">
                        Next Round
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

@endsection
