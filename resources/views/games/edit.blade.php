@extends('layouts.app')

@section('page-title', 'Edit Ball Game')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the details of the ball game below.
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
                @foreach($divisions as $division)
                    <option value="{{ $division->name }}"
                        {{ old('team_a', $game->team_a) == $division->name ? 'selected' : '' }}>
                        {{ $division->name }}
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
                @foreach($divisions as $division)
                    <option value="{{ $division->name }}"
                        {{ old('team_b', $game->team_b) == $division->name ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Additional Teams --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Additional Teams (if any)</label>
            <div id="additionalTeamsContainer">
                @php
                    $additionalTeams = $game->teams ?? collect();
                @endphp

                @if($additionalTeams->count() > 0)
                    @foreach($additionalTeams as $team)
                        <div class="flex mb-2">
                            <select name="additional_teams[]" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->name }}"
                                        {{ $team->team_name == $division->name ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="removeTeam(this)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Remove</button>
                        </div>
                    @endforeach
                @else
                    <div class="flex mb-2">
                        <select name="additional_teams[]" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->name }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="removeTeam(this)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Remove</button>
                    </div>
                @endif
            </div>
            <button type="button" onclick="addTeam()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded">Add Another Team</button>
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

<script>
function addTeam() {
    const container = document.getElementById('additionalTeamsContainer');
    const div = document.createElement('div');
    div.classList.add('flex', 'mb-2');
    div.innerHTML = `
        <select name="additional_teams[]" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            <option value="">Select Division</option>
            @foreach($divisions as $division)
                <option value="{{ $division->name }}">{{ $division->name }}</option>
            @endforeach
        </select>
        <button type="button" onclick="removeTeam(this)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Remove</button>
    `;
    container.appendChild(div);
}

function removeTeam(button) {
    button.parentElement.remove();
}
</script>
@endsection
