@extends('layouts.app')

@section('page-title', 'Add Game')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Add New Game</h1>
    <p class="text-sm text-gray-600 mb-6">
        Fill in the details of the game you want to register.
    </p>

    <form action="{{ route('games.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Event</label>
            <select name="event_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>
            @error('event_id')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Team A</label>
            <input type="text" name="team_a" value="{{ old('team_a') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('team_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Team B</label>
            <input type="text" name="team_b" value="{{ old('team_b') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Game Time</label>
            <input type="datetime-local" name="game_time" value="{{ old('game_time') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('game_time')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Venue</label>
            <input type="text" name="venue" value="{{ old('venue') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('venue')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Save Game
        </button>
    </form>
</div>
@endsection
