@extends('layouts.app')

@section('page-title', 'Edit Game')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Game</h1>
    <p class="text-sm text-gray-600 mb-6">
        Update the details of the game.
    </p>

    <form action="{{ route('games.update', $game->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Event</label>
            <select name="event_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $game->event_id == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>
            @error('event_id')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Team A</label>
            <input type="text" name="team_a" value="{{ old('team_a', $game->team_a) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('team_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Team B</label>
            <input type="text" name="team_b" value="{{ old('team_b', $game->team_b) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Score A</label>
            <input type="number" name="score_a" value="{{ old('score_a', $game->score_a) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('score_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Score B</label>
            <input type="number" name="score_b" value="{{ old('score_b', $game->score_b) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('score_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach(['Upcoming', 'Ongoing', 'Finished'] as $status)
                    <option value="{{ $status }}" {{ $game->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Game Time</label>
            <input type="datetime-local" name="game_time" value="{{ old('game_time', \Carbon\Carbon::parse($game->game_time)->format('Y-m-d\TH:i')) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('game_time')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Venue</label>
            <input type="text" name="venue" value="{{ old('venue', $game->venue) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('venue')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Update Game
        </button>
    </form>
</div>
@endsection
