@extends('layouts.app')

@section('page-title', 'Games / Matches')

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

@if($games->isEmpty())
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center text-gray-500 dark:text-gray-300">
        No games found.
    </div>
@else
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">

    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Event</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Team A</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Team B</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Score</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Time</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">Venue</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($games as $game)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 text-gray-800">{{ $game->event->name }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $game->team_a }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $game->team_b }}</td>
                        <td class="px-4 py-2 text-gray-800">
                            {{ $game->score_a ?? '-' }} - {{ $game->score_b ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-gray-800">
    <span class="
        @if($game->current_status === 'Upcoming') text-blue-600
        @elseif($game->current_status === 'Ongoing') text-green-600
        @else text-gray-600
        @endif
        font-semibold">
        {{ $game->current_status }}
    </span>
</td>




                        <td class="px-4 py-2 text-gray-800">{{ $game->game_time }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $game->venue }}</td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('games.edit', $game->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold mr-2">Edit</a>
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
@endsection
