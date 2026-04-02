@extends('layouts.app')

@section('page-title', 'Edit Competition')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600">
        Update the details of this CAVRAA competition.
    </p>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">
    <form action="{{ route('events.update', $event->id) }}" method="POST"
          x-data="{
            sports: @js($sports),
            selectedSportId: {{ $event->sport_id }},
            sportTypes: @js($event->sport->sportTypes),
            level: '{{ $event->age_group }}',
            isPara: {{ $event->sport->division === 'Para Games' ? 'true' : 'false' }},
            divisions: @js(old('divisions', $event->divisions->pluck('id')->toArray())),
            games: @js(old('games', collect($event->games ?? [])
                    ->map(fn($g) => ['home' => explode(' vs ', $g)[0] ?? '', 'away' => explode(' vs ', $g)[1] ?? ''])
                    ->values()
                )),
            prepareGames() {
                return this.games
                    .filter(g => g.home && g.away)
                    .map(g => `${g.home} vs ${g.away}`);
            },
            addGame() {
                this.games.push({home:'', away:''});
            }
          }"
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
          class="space-y-6">

        @csrf
        @method('PUT')

        <input type="hidden" name="status" value="{{ old('status', $event->status) }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Sport --}}
            <div>
                <label class="block font-medium">Sport *</label>
                <select name="sport_id" x-model="selectedSportId" class="w-full border rounded px-3 py-2" required>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" :selected="selectedSportId == {{ $sport->id }}">
                            {{ $sport->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sport Type --}}
            <div>
                <label class="block font-medium">Sport Type *</label>
                <select name="sport_type_id" class="w-full border rounded px-3 py-2" required>
                    <template x-for="type in sportTypes" :key="type.id">
                        <option :value="type.id" :selected="type.id == {{ $event->sport_type_id }}">
                            <span x-text="type.type_name"></span>
                        </option>
                    </template>
                </select>
            </div>

            {{-- Age Group --}}
            <div>
                <label class="block font-medium">Age Group</label>
                <template x-if="isPara">
                    <input type="text" name="age_group" x-model="level" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
                </template>
                <template x-if="!isPara">
                    <select name="age_group" x-model="level" class="w-full border rounded px-3 py-2">
                        <option value="">Select Level</option>
                        <option value="Elementary">Elementary</option>
                        <option value="Secondary">Secondary</option>
                    </select>
                </template>
            </div>

            {{-- Category --}}
            <div>
                <label class="block font-medium">Category</label>
                <select name="category" class="w-full border rounded px-3 py-2">
                    @foreach(['Boys','Girls','Mixed'] as $cat)
                        <option value="{{ $cat }}" {{ $event->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Venue --}}
            <div>
                <label class="block font-medium">Venue</label>
                <input type="text" name="venue" value="{{ $event->venue }}" class="w-full border rounded px-3 py-2">
            </div>

            {{-- Start Date --}}
            <div>
                <label class="block font-medium">Start Date</label>
                <input type="date" name="start_date" value="{{ optional($event->start_date)->format('Y-m-d') }}" class="w-full border rounded px-3 py-2">
            </div>

        </div>

        {{-- PARTICIPATING DIVISIONS --}}
        <div class="mt-6" x-data="{ divisions: @js(old('divisions', $event->divisions->pluck('id')->toArray())) }">
            <label class="block font-semibold mb-2">Participating Divisions *</label>

            <template x-for="(division, index) in divisions" :key="index">
                <div class="flex gap-2 mb-2">
                    <select name="divisions[]" x-model="divisions[index]" class="flex-1 border rounded px-3 py-2" required>
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" x-show="divisions.length > 1" @click="divisions.splice(index, 1)" class="px-3 bg-red-500 text-white rounded">✕</button>
                </div>
            </template>

            <button type="button" @click="divisions.push('')" class="mt-2 px-4 py-2 bg-green-600 text-white rounded">
                + Add Another Division
            </button>
        </div>

        {{-- 🔥 GAMES SECTION --}}
        <div class="pt-6 border-t">
            <label class="block font-semibold mb-3">Games / Matches <span class="text-sm text-gray-500">(Auto-labeled)</span></label>

            <template x-for="(game, idx) in games" :key="idx">
                <div class="mb-4 p-4 border rounded bg-gray-50">
                    <div class="font-semibold mb-2">Game <span x-text="idx + 1"></span></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select x-model="game.home" class="border rounded px-3 py-2" required>
                            <option value="">Home Division</option>
                            @foreach($divisions as $division)
                                <option :value="'{{ $division->name }}'">{{ $division->name }}</option>
                            @endforeach
                        </select>

                        <select x-model="game.away" class="border rounded px-3 py-2" required>
                            <option value="">Away Division</option>
                            @foreach($divisions as $division)
                                <option :value="'{{ $division->name }}'">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" x-show="games.length > 1" @click="games.splice(idx,1)" class="mt-3 bg-red-500 text-white px-3 py-1 rounded">
                        Remove Game
                    </button>
                </div>
            </template>

            <button type="button" @click="games.push({home:'', away:''})" class="mt-2 px-4 py-2 bg-green-600 text-white rounded">
                + Add Game
            </button>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-6">
            <a href="{{ route('events.index') }}" class="px-4 py-2 border rounded text-gray-700">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-semibold">Update Competition</button>
        </div>

    </form>
</div>

@endsection
