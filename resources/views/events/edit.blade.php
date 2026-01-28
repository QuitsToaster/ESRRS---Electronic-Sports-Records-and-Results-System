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
    <form action="{{ route('events.update', $event->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Sport --}}
            <div>
                <label class="block font-medium">Sport *</label>
                <select name="sport_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Sport</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}"
                            {{ old('sport_id', $event->sport_id) == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sport Type --}}
            <div>
                <label class="block font-medium">Sport Type *</label>
                <select name="sport_type_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Type</option>

                    @foreach($event->sport->sportTypes as $type)
                        <option value="{{ $type->id }}"
                            {{ old('sport_type_id', $event->sport_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->type_name }}
                        </option>
                    @endforeach
                </select>

                <p class="text-xs text-gray-500 mt-1">
                    Event = multi-player (Swimming, Athletics) • Game = team vs team
                </p>
            </div>

            {{-- Age Group --}}
            <div>
                <label class="block font-medium">Age Group</label>
                <select name="age_group" class="w-full border rounded px-3 py-2">
                    <option value="">Select Age Group</option>
                    @foreach(['Elementary','Secondary'] as $group)
                        <option value="{{ $group }}"
                            {{ old('age_group', $event->age_group) == $group ? 'selected' : '' }}>
                            {{ $group }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Category --}}
            <div>
                <label class="block font-medium">Category</label>
                <select name="category" class="w-full border rounded px-3 py-2">
                    <option value="">Select Category</option>
                    @foreach(['Boys','Girls','Mixed'] as $cat)
                        <option value="{{ $cat }}"
                            {{ old('category', $event->category) == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Venue --}}
            <div>
                <label class="block font-medium">Venue</label>
                <input type="text"
                       name="venue"
                       value="{{ old('venue', $event->venue) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- Start Date --}}
            <div>
                <label class="block font-medium">Start Date</label>
                <input type="date"
                       name="start_date"
                       value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

        </div>

        {{-- PARTICIPATING SCHOOLS --}}
        <div x-data="{
                schools: {{ json_encode(old('schools', $event->schools->pluck('id')->toArray())) }}
            }"
            class="mt-6">

            <label class="block font-semibold mb-2">
                Participating Schools / Teams *
            </label>

            <template x-for="(school, index) in schools" :key="index">
                <div class="flex gap-2 mb-2">
                    <select name="schools[]"
                            x-model="schools[index]"
                            class="flex-1 border rounded px-3 py-2"
                            required>
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button"
                            x-show="schools.length > 1"
                            @click="schools.splice(index, 1)"
                            class="px-3 bg-red-500 text-white rounded">
                        ✕
                    </button>
                </div>
            </template>

            <button type="button"
                    @click="schools.push('')"
                    class="mt-2 px-4 py-2 bg-green-600 text-white rounded">
                + Add Another School
            </button>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-6">
            <a href="{{ route('events.index') }}"
               class="px-4 py-2 border rounded text-gray-700">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded font-semibold">
                Update Competition
            </button>
        </div>

    </form>
</div>

@endsection
