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
            <th class="px-4 py-2 text-left font-medium text-gray-700">Bracket / Match</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Team A</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Team B</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Score</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Winner</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Time</th>
            <th class="px-4 py-2 text-left font-medium text-gray-700">Venue</th>
            <th class="px-4 py-2 text-right font-medium text-gray-700">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @foreach($games as $game)
            @php
                $scores = is_array($game->scores)
                    ? $game->scores
                    : (is_string($game->scores) ? json_decode($game->scores, true) : []);

                $scores = $scores ?? ['team_a' => [], 'team_b' => []];

                $scoreA = array_sum($scores['team_a'] ?? []);
                $scoreB = array_sum($scores['team_b'] ?? []);

                $status = $game->status ?? 'Upcoming';

                // Determine bracket/match info
                $bracketName = '';
                if($brackets->isNotEmpty()) {
                    foreach($brackets as $bracket) {
                        $matches = json_decode($bracket->structure, true);
                        foreach($matches as $index => $match) {
                            if(
                                ($match['team_a'] === $game->team_a && $match['team_b'] === $game->team_b) ||
                                ($match['team_a'] === $game->team_b && $match['team_b'] === $game->team_a)
                            ) {
                                $bracketName = $bracket->event->name . ' - Match ' . ($index + 1);
                            }
                        }
                    }
                }
            @endphp

            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-2">{{ $game->event->name }}</td>
                <td class="px-4 py-2">{{ $bracketName ?: 'N/A' }}</td>
                <td class="px-4 py-2">{{ $game->team_a }}</td>
                <td class="px-4 py-2">{{ $game->team_b }}</td>
                <td class="px-4 py-2 font-semibold">{{ $scoreA }} - {{ $scoreB }}</td>

                {{-- WINNER COLUMN --}}
                <td class="px-4 py-2">
                    @if($game->winner)
                        <span class="text-green-700 font-bold">
                            {{ $game->winner }}
                        </span>
                    @else
                        <span class="text-gray-400 italic">TBD</span>
                    @endif
                </td>

                {{-- STATUS --}}
                <td class="px-4 py-2">
                    <span class="
                        @if($status === 'Upcoming') text-blue-600
                        @elseif($status === 'Ongoing') text-green-600
                        @else text-gray-600
                        @endif
                        font-semibold">
                        {{ $status }}
                    </span>
                </td>

                <td class="px-4 py-2">{{ $game->game_time }}</td>
                <td class="px-4 py-2">{{ $game->venue }}</td>

                {{-- ACTIONS --}}
                <td class="px-4 py-2 text-right">
                    <a href="{{ route('games.edit', $game->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold mr-2">
                        Edit
                    </a>
                    <a href="{{ route('games.manage', $game->id) }}" class="text-green-600 hover:text-green-800 font-semibold mr-2">
                        Manage
                    </a>
                    <form action="{{ route('games.destroy', $game->id) }}" method="POST" class="inline-block"
                          onsubmit="return confirm('Are you sure you want to delete this game?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif

{{-- Tournament Bracket Section --}}
@if($games->isNotEmpty())
<h2 class="text-2xl font-bold text-gray-800 mb-6">Tournament Bracket</h2>

@php
$roundOrder = ['Round 1', 'Quarterfinal', 'Semifinal', 'Final', 'Bronze'];
$grouped = $games->groupBy('round');
@endphp

<style>
/* Bracket container */
.bracket-container {
    display: flex;
    gap: 60px;
    overflow-x: auto;
    padding-bottom: 40px;
}

/* Each round column */
.bracket-round {
    display: flex;
    flex-direction: column;
    gap: 40px;
    min-width: 200px;
    position: relative;
}

/* Each match box */
.bracket-match {
    background: white;
    border: 2px solid #ddd;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    position: relative;
}

/* Team styling */
.team {
    font-weight: 500;
    margin: 4px 0;
}

.team.winner {
    font-weight: 700;
    color: #16a34a; /* green */
}

/* Connector lines to next round */
.bracket-match::after {
    content: '';
    position: absolute;
    width: 40px;
    height: 2px;
    background: #888;
    top: 50%;
    right: -40px;
}

.bracket-match:last-child::after {
    display: none;
}

/* Vertical connector to align two matches into next match */
.bracket-connector {
    position: absolute;
    width: 2px;
    height: 40px;
    background: #888;
    top: 50%;
    right: -40px;
}
</style>

<div class="bracket-container">
    @foreach($roundOrder as $roundIndex => $round)
        @if(isset($grouped[$round]))
            <div class="bracket-round">
                <h4 class="text-center font-bold text-gray-700 mb-2">{{ $round }}</h4>

                @foreach($grouped[$round]->sortBy('match_number') as $game)
                    <div class="bracket-match">
                        <div class="team {{ $game->winner === $game->team_a ? 'winner' : '' }}">
                            {{ $game->team_a ?? 'TBD' }}
                        </div>

                        <div class="text-gray-400 text-sm my-1">vs</div>

                        <div class="team {{ $game->winner === $game->team_b ? 'winner' : '' }}">
                            {{ $game->team_b ?? 'TBD' }}
                        </div>

                        <div class="text-xs text-gray-500 mt-2">{{ $game->event->name }}</div>

                        <a href="{{ route('games.manage', $game->id) }}"
                           class="text-blue-600 text-sm block mt-2 font-semibold">
                            Manage →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
@endif



@endsection
