@extends('layouts.app')

@section('page-title', 'Add Event')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600">
        Select a sport and its discipline. Event details will be generated automatically.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">

<form action="{{ route('events.store') }}" method="POST"
      x-data="{
        sports: @js($sports),
        selectedSport: null,
        sportTypes: []
      }"
      class="space-y-4">
@csrf

{{-- SPORT --}}
<div>
    <label class="block font-semibold">Sport *</label>
    <select name="sport_id"
            x-model="selectedSport"
            @change="sportTypes = sports.find(s => s.id == selectedSport)?.sport_types || []"
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
    <label class="block font-semibold">Discipline / Event *</label>
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

{{-- 🔥 BALL GAME FLAG --}}
<div class="border rounded p-4 bg-gray-50">
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_ball_game" value="1"
               class="rounded border-gray-300">
        <span class="font-semibold">This is a Ball Game</span>
    </label>
    <p class="text-xs text-gray-500 mt-1">
        If checked, this event will automatically be added to the Games module.
    </p>
</div>

{{-- LEVEL --}}
<div>
    <label class="block font-semibold">Level</label>
    <select name="age_group" class="w-full border rounded px-3 py-2">
        <option value="">Select Level</option>
        <option value="Elementary">Elementary</option>
        <option value="Secondary">Secondary</option>
    </select>
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
    <label class="block font-semibold">Start Date</label>
    <input type="date" name="start_date" class="w-full border rounded px-3 py-2">
</div>

{{-- DIVISIONS --}}
<div x-data="{ divisionsList: [0] }">
    <label class="block font-semibold mb-2">Participating Divisions *</label>

    <template x-for="(division, index) in divisionsList" :key="index">
        <div class="flex gap-2 mb-2">
            <select name="divisions[]" required class="flex-1 border rounded px-3 py-2">
                <option value="">Select Division</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>

            <button type="button"
                    x-show="divisionsList.length > 1"
                    @click="divisionsList.splice(index, 1)"
                    class="bg-red-500 text-white px-3 rounded">
                ✕
            </button>
        </div>
    </template>

    <button type="button"
            @click="divisionsList.push(divisionsList.length)"
            class="mt-2 bg-green-600 text-white px-4 py-2 rounded">
        + Add Division
    </button>
</div>

{{-- ACTIONS --}}
<div class="flex justify-end gap-2 pt-4">
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
