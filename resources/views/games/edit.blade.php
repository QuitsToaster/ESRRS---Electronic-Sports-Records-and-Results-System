@extends('layouts.app')

@section('page-title', 'Edit Game')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Edit Game</h1>

    <form action="{{ route('games.update', $game->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Event</label>
            <select name="event_id" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $game->event_id == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>
            @error('event_id')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Team A</label>
            <input type="text" name="team_a" value="{{ old('team_a', $game->team_a) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('team_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Team B</label>
            <input type="text" name="team_b" value="{{ old('team_b', $game->team_b) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Score A</label>
            <input type="number" name="score_a" value="{{ old('score_a', $game->score_a) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('score_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Score B</label>
            <input type="number" name="score_b" value="{{ old('score_b', $game->score_b) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('score_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Status</label>
            <select name="status" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                @foreach(['Upcoming', 'Ongoing', 'Finished'] as $status)
                    <option value="{{ $status }}" {{ $game->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Game Time</label>
            <input type="datetime-local" name="game_time" value="{{ old('game_time', \Carbon\Carbon::parse($game->game_time)->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('game_time')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Venue</label>
            <input type="text" name="venue" value="{{ old('venue', $game->venue) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('venue')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Update Game
        </button>
    </form>
</div>
@endsection
