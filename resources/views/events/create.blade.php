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
      }"
      x-init="
        $watch('selectedSportId', value => {
            let sport = sports.find(s => s.id == value);

            sportTypes = sport ? sport.sport_types : [];
            isPara = sport && sport.division === 'Paragames';

            // Reset level ONLY when sport changes
            level = isPara ? 'Para Games' : '';
        });
      "
      class="space-y-4">

@csrf

{{-- SPORT --}}
<div>
    <label class="block font-semibold">Sport *</label>
    <select name="sport_id"
            x-model="selectedSportId"
            required
            class="w-full border rounded px-3 py-2">
        <option value="">Select Sport</option>
        <template x-for="sport in sports" :key="sport.id">
            <option :value="sport.id" x-text="sport.name"></option>
        </template>
    </select>
</div>

{{-- SPORT TYPE --}}
<div>
    <label class="block font-semibold">Discipline *</label>
    <select name="sport_type_id"
            :disabled="sportTypes.length === 0"
            required
            class="w-full border rounded px-3 py-2">
        <option value="">Select Discipline</option>
        <template x-for="type in sportTypes" :key="type.id">
            <option :value="type.id" x-text="type.type_name"></option>
        </template>
    </select>
</div>

{{-- LEVEL --}}
<div>
    <label class="block font-semibold">Level *</label>

    {{-- PARA --}}
    <template x-if="isPara">
        <input type="text"
               name="age_group"
               x-model="level"
               readonly
               class="w-full border rounded px-3 py-2 bg-gray-100">
    </template>

    {{-- REGULAR --}}
    <template x-if="!isPara">
        <select name="age_group"
                x-model="level"
                required
                class="w-full border rounded px-3 py-2">
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
        <span class="text-sm text-gray-500">(All included by default — remove if needed)</span>
    </label>

    <template x-for="(divisionId, index) in divisionsList" :key="divisionId">
        <div class="flex gap-2 mb-2 items-center">
            <select name="divisions[]"
                    required
                    class="flex-1 border rounded px-3 py-2"
                    x-model="divisionsList[index]">
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>

            <button type="button"
                    x-show="divisionsList.length > 1"
                    @click="divisionsList.splice(index, 1)"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">
                ✕
            </button>
        </div>
    </template>

    <p class="text-xs text-gray-500 mt-1">
        For ball games, remove extra divisions until only two remain.
    </p>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-300 rounded">
        Cancel
    </a>
    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
        Save Event
    </button>
</div>

</form>
</div>
@endsection
