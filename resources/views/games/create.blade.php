@extends('layouts.app')

@section('page-title', 'Add Game')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100">Add New Game</h1>

    <form action="{{ route('games.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Event</label>
            <select name="event_id" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                <option value="">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>
            @error('event_id')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Team A</label>
            <input type="text" name="team_a" value="{{ old('team_a') }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('team_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Team B</label>
            <input type="text" name="team_b" value="{{ old('team_b') }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Game Time</label>
            <input type="datetime-local" name="game_time" value="{{ old('game_time') }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('game_time')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Venue</label>
            <input type="text" name="venue" value="{{ old('venue') }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            @error('venue')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Save Game
        </button>
    </form>
</div>
@endsection
