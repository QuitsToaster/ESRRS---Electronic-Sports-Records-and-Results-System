@extends('layouts.app')

@section('page-title', 'Add Event')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600">
        Select a sport and discipline. Event details will adjust automatically.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">

<form action="{{ route('events.store') }}" method="POST"
      x-data="{
        sports: @js($sports),
        selectedSportId: '',
        sportTypes: [],
        level: '',
        isPara: false,
        divisionsList: @js($divisions->pluck('id')),
        games: [
            { home: '', away: '' }
        ],
        prepareGames() {
            return this.games
                .filter(g => g.home && g.away)
                .map(g => `${g.home} vs ${g.away}`);
        }
      }"
      x-init="
        $watch('selectedSportId', value => {
            let sport = sports.find(s => s.id == value);
            sportTypes = sport ? sport.sport_types : [];
            isPara = sport && sport.division === 'Para Games';
            level = isPara ? 'Para Games' : '';
        });
      "
      @submit="
        let prepared = prepareGames();
        prepared.forEach(g => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'games[]';
            input.value = g;
            $el.appendChild(input);
        });
      "
      class="space-y-4">

@csrf

{{-- SPORT --}}
<div>
    <label class="block font-semibold">Sport *</label>
    <select name="sport_id" x-model="selectedSportId" required class="w-full border rounded px-3 py-2">
        <option value="">Select Sport</option>
        <template x-for="sport in sports" :key="sport.id">
            <option :value="sport.id" x-text="sport.name"></option>
        </template>
    </select>
</div>

{{-- DISCIPLINE --}}
<div>
    <label class="block font-semibold">Discipline *</label>
    <select name="sport_type_id" :disabled="sportTypes.length === 0" required class="w-full border rounded px-3 py-2">
        <option value="">Select Discipline</option>
        <template x-for="type in sportTypes" :key="type.id">
            <option :value="type.id" x-text="type.type_name"></option>
        </template>
    </select>
</div>

{{-- LEVEL --}}
<div>
    <label class="block font-semibold">Level *</label>

    <template x-if="isPara">
        <input type="text" name="age_group" x-model="level" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
    </template>

    <template x-if="!isPara">
        <select name="age_group" x-model="level" required class="w-full border rounded px-3 py-2">
            <option value="">Select Level</option>
            <option value="Elementary">Elementary</option>
            <option value="Secondary">Secondary</option>
        </select>
    </template>
</div>

{{-- CATEGORY --}}
<div>
    <label class="block font-semibold">Category</label>
    <select name="category" class="w-full border rounded px-3 py-2">
        <option value="">Select Category</option>
        <option value="Boys">Boys</option>
        <option value="Girls">Girls</option>
        <option value="Mixed">Mixed</option>
    </select>
</div>

{{-- VENUE --}}
<div>
    <label class="block font-semibold">Venue</label>
    <input type="text" name="venue" class="w-full border rounded px-3 py-2">
</div>

{{-- DATE --}}
<div>
    <label class="block font-semibold">Event Date</label>
    <input type="date" name="start_date" class="w-full border rounded px-3 py-2">
</div>

{{-- PARTICIPATING DIVISIONS --}}
<div>
    <label class="block font-semibold mb-2">
        Participating Divisions *
        <span class="text-sm text-gray-500">(All included by default)</span>
    </label>

    <template x-for="(divisionId, idx) in divisionsList" :key="idx">
        <div class="flex gap-2 mb-2 items-center">
            <select name="divisions[]"
                    x-model="divisionsList[idx]"
                    class="flex-1 border rounded px-3 py-2"
                    required>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>

            <button type="button"
                    x-show="divisionsList.length > 1"
                    @click="divisionsList.splice(idx,1)"
                    class="bg-red-500 text-white px-3 py-2 rounded">
                ✕
            </button>
        </div>
    </template>
</div>

{{-- 🔥 GAMES SECTION --}}
<div class="pt-6 border-t">
    <label class="block font-semibold mb-3">
        Games / Matches
        <span class="text-sm text-gray-500">(Auto-labeled)</span>
    </label>

    <template x-for="(game, idx) in games" :key="idx">
        <div class="mb-4 p-4 border rounded bg-gray-50">
            <div class="font-semibold mb-2">
                Game <span x-text="idx + 1"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <select x-model="game.home" class="border rounded px-3 py-2">
                    <option value="">Home Division</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->name }}">{{ $division->name }}</option>
                    @endforeach
                </select>

                <select x-model="game.away" class="border rounded px-3 py-2">
                    <option value="">Away Division</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->name }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="button"
                    x-show="games.length > 1"
                    @click="games.splice(idx,1)"
                    class="mt-3 bg-red-500 text-white px-3 py-1 rounded">
                Remove Game
            </button>
        </div>
    </template>

    <button type="button"
            @click="games.push({ home:'', away:'' })"
            class="mt-2 px-4 py-2 bg-green-600 text-white rounded">
        + Add Game
    </button>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-2 pt-6">
    <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-300 rounded">
        Cancel
    </a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">
        Save Event
    </button>
</div>

</form>
</div>
@endsection
