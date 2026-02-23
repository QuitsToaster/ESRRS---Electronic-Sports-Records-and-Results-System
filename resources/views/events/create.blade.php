@extends('layouts.app')

@section('page-title', 'Add Event')

@section('content')

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>Select a sport to auto-fill the level and category fields.</li>
        <li>Use the search box to quickly find sports by name, division, or category.</li>
        <li>Click on a sport from the search results to automatically select it.</li>
        <li>Add participating divisions and matches as needed.</li>
        <li>Fill in the venue and event date before saving.</li>
        <li>Click <strong>Save Event</strong> to register the competition.</li>
    </ul>
</div>

<div class="mb-6">
    <p class="text-sm text-gray-600">
        Select a sport. Level and category will be set automatically.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">

<form action="{{ route('events.store') }}" method="POST"
      x-data="{
        sports: @js($sports),
        selectedSportId: '',
        sportTypes: [],
        level: '',
        category: '',
        isLocked: false,
        divisionsList: @js($divisions->pluck('id')),
        games: [{ home: '', away: '' }],
        searchTerm: '',
        showResults: false,

        prepareGames() {
            return this.games
                .filter(g => g.home && g.away)
                .map(g => `${g.home} vs ${g.away}`);
        },

        filteredSports() {
            if (!this.searchTerm) return [];
            return this.sports.filter(s =>
                s.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                (s.division ?? '').toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                (s.category ?? '').toLowerCase().includes(this.searchTerm.toLowerCase())
            );
        },

        selectSport(sport) {
            this.selectedSportId = sport.id;
            this.searchTerm = sport.name;
            this.showResults = false; // Close dropdown
            let event = new Event('input', { bubbles: true });
            this.$refs.sportDropdown.dispatchEvent(event);
        }
      }"
      x-init="
        $watch('selectedSportId', value => {
            let sport = sports.find(s => s.id == value);
            if (sport) {
                sportTypes = sport.sport_types ?? [];
                level = sport.division ?? '';
                category = sport.category ?? '';
                isLocked = !!(sport.division || sport.category);
            } else {
                sportTypes = [];
                level = '';
                category = '';
                isLocked = false;
            }
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

{{-- SPORT SEARCH --}}
<div class="relative">
    <label class="block font-semibold">Sport *</label>
    <input type="text" placeholder="Search sports..."
           x-model="searchTerm"
           @focus="showResults = true"
           @keydown.escape="showResults = false"
           @click.away="showResults = false"
           class="w-full border rounded px-3 py-2 mb-1">

    {{-- Live search results --}}
    <div x-show="showResults && filteredSports().length > 0" 
         class="absolute z-10 w-full bg-white border border-gray-300 rounded shadow max-h-60 overflow-y-auto mt-1">
        <template x-for="sport in filteredSports()" :key="sport.id">
            <div @click="selectSport(sport)"
                 class="px-3 py-2 cursor-pointer hover:bg-blue-100">
                <span x-text="`${sport.name} — ${sport.division ?? 'N/A'} | ${sport.category ?? 'N/A'}`"></span>
            </div>
        </template>
    </div>

    {{-- Hidden dropdown to store actual value --}}
    <select name="sport_id" x-ref="sportDropdown" x-model="selectedSportId" hidden required>
        <option value="">Select Sport</option>
        <template x-for="sport in sports" :key="sport.id">
            <option :value="sport.id" x-text="sport.name"></option>
        </template>
    </select>
</div>

{{-- Type --}}
<div>
    <label class="block font-semibold">Type *</label>
    <select name="sport_type_id"
            :disabled="sportTypes.length === 0"
            required
            class="w-full border rounded px-3 py-2">
        <option value="">Select Type</option>
        <template x-for="type in sportTypes" :key="type.id">
            <option :value="type.id" x-text="type.type_name"></option>
        </template>
    </select>
</div>

{{-- LEVEL (AUTO) --}}
<div>
    <label class="block font-semibold">Level *</label>
    <input type="text"
           name="age_group"
           x-model="level"
           readonly
           class="w-full border rounded px-3 py-2 bg-gray-100">
</div>

{{-- CATEGORY (AUTO) --}}
<div>
    <label class="block font-semibold">Category *</label>
    <input type="text"
           name="category"
           x-model="category"
           readonly
           class="w-full border rounded px-3 py-2 bg-gray-100">
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

{{-- GAMES --}}
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
