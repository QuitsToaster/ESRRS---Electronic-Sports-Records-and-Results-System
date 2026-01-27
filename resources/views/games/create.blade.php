@extends('layouts.app')

@section('page-title', 'Add Game')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Add a new game to the system by filling out the form below.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">
    <form action="{{ route('games.store') }}" method="POST">
        @csrf

        {{-- Event --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Event</label>
            <select name="event_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}"
                        {{ old('event_id') == $event->id ? 'selected' : '' }}>
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
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->name }}"
                        {{ old('team_a') == $school->name ? 'selected' : '' }}>
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
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->name }}"
                        {{ old('team_b') == $school->name ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            @error('team_b')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Additional Teams --}}
<div class="mb-4">
    <label class="block text-gray-700 font-semibold mb-1">Additional Teams (if any)</label>
    <div id="additionalTeamsContainer">
        <div class="flex mb-2">
            <select name="additional_teams[]" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->name }}">{{ $school->name }}</option>
                @endforeach
            </select>
            <button type="button" onclick="removeTeam(this)" class="ml-2 px-3 py-1 bg-red-500 text-white rounded">Remove</button>
        </div>
    </div>
    <button type="button" onclick="addTeam()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded">Add Another Team</button>
</div>

<script>
function addTeam() {
    const container = document.getElementById('additionalTeamsContainer');
    const div = document.createElement('div');
    div.classList.add('flex', 'mb-2');
    div.innerHTML = `
        <select name="additional_teams[]" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            <option value="">Select School</option>
            @foreach($schools as $school)
                <option value="{{ $school->name }}">{{ $school->name }}</option>
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


        {{-- Game Time --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">Game Time</label>
            <input type="datetime-local" name="game_time"
                   value="{{ old('game_time') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('game_time')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Venue --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-1">Venue</label>
            <input type="text" name="venue"
                   value="{{ old('venue') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('venue')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Save Game
        </button>
    </form>
</div>
@endsection
