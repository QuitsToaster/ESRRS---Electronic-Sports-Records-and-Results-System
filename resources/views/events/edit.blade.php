@extends('layouts.app')

@section('page-title', 'Edit Event')

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <p class="text-sm text-gray-600">
        Update the details of this CAVRAA event.
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

{{-- Edit Event Form --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">
    <form action="{{ route('events.update', $event->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Event Name (REQUIRED) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Event Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $event->name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500"
                       required>
            </div>

            {{-- Sport (OPTIONAL) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sport
                </label>
                <select name="sport_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                    <option value="">Select Sport</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}"
                            {{ old('sport_id', $event->sport_id) == $sport->id ? 'selected' : '' }}>
                            {{ $sport->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Level --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Level
                </label>
                <select name="level"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Level</option>
                    @foreach(['Division', 'Regional', 'National'] as $level)
                        <option value="{{ $level }}"
                            {{ old('level', $event->level) == $level ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Age Group --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Age Group
                </label>
                <select name="age_group"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Age Group</option>
                    @foreach(['Elementary', 'Secondary'] as $group)
                        <option value="{{ $group }}"
                            {{ old('age_group', $event->age_group) == $group ? 'selected' : '' }}>
                            {{ $group }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Category
                </label>
                <select name="category"
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Select Category</option>
                    @foreach(['Boys', 'Girls', 'Mixed'] as $category)
                        <option value="{{ $category }}"
                            {{ old('category', $event->category) == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Venue --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Venue
                </label>
                <input type="text"
                       name="venue"
                       value="{{ old('venue', $event->venue) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- Start Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Start Date
                </label>
                <input type="date"
                       name="start_date"
                       value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- End Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    End Date
                </label>
                <input type="date"
                       name="end_date"
                       value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

        </div>

        {{-- Actions --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('events.index') }}"
               class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-semibold">
                Update Event
            </button>
        </div>
    </form>
</div>

@endsection
