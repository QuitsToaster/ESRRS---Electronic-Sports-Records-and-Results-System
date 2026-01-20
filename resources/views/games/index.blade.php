@extends('layouts.app')

@section('page-title', 'Games / Matches')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Games / Matches</h1>
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
    <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <table class="min-w-full border border-gray-200 dark:border-gray-700">
            <thead class="bg-blue-200 dark:bg-blue-900">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Event</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Team A</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Team B</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Score</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Status</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Time</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Venue</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-700 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($games as $game)
                    <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $game->event->name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $game->team_a }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $game->team_b }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">
                            {{ $game->score_a ?? '-' }} - {{ $game->score_b ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $game->status }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $game->game_time }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $game->venue }}</td>
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
