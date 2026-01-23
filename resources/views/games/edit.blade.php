@extends('layouts.app')

@section('page-title', 'Edit Game')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the details of the game below.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">
    <form action="{{ route('games.update', $game->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Event --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Event</label>
            <select name="event_id"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @foreach($events as $event)
                    <option value="{{ $event->id }}"
                        {{ $game->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
            @error('event_id')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Team A --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Team A</label>
            <select name="team_a"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @foreach($schools as $school)
                    <option value="{{ $school->name }}"
                        {{ old('team_a', $game->team_a) == $school->name ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            @error('team_a')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Team B --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Team B</label>
            <select name="team_b"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @foreach($schools as $school)
                    <option value="{{ $school->name }}"
                        {{ old('team_b', $game->team_b) == $school->name ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Scores --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Score A</label>
            <input type="number" name="score_a"
                   value="{{ old('score_a', $game->score_a) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Score B</label>
            <input type="number" name="score_b"
                   value="{{ old('score_b', $game->score_b) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Status</label>
            <select name="status"
                class="w-full border border-gray-300 rounded px-3 py-2">
                @foreach(['Upcoming', 'Ongoing', 'Finished'] as $status)
                    <option value="{{ $status }}"
                        {{ $game->status == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Game Time --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Game Time</label>
            <input type="datetime-local" name="game_time"
                   value="{{ \Carbon\Carbon::parse($game->game_time)->format('Y-m-d\TH:i') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        {{-- Venue --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-1">Venue</label>
            <input type="text" name="venue"
                   value="{{ old('venue', $game->venue) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Update Game
        </button>
    </form>
</div>
@endsection
