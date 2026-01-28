@extends('layouts.app')

@section('page-title', 'Manage Event')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            Manage Event: {{ $event->name }}
        </h2>
        <p class="text-sm text-gray-600">
            {{ $event->category ?? 'Uncategorized' }}
        </p>
    </div>

    <a href="{{ route('events.index') }}"
       class="text-blue-600 hover:text-blue-800 font-semibold">
        ← Back to Events
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

{{-- Event Info --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div>
            <span class="text-gray-500">Venue</span>
            <p class="font-semibold">{{ $event->venue ?? 'TBA' }}</p>
        </div>

        <div>
            <span class="text-gray-500">Start Date</span>
            <p class="font-semibold">
                {{ $event->start_date?->format('M d, Y') ?? 'TBA' }}
            </p>
        </div>

        <div>
            <span class="text-gray-500">Age Group</span>
            <p class="font-semibold">{{ $event->age_group ?? 'TBA' }}</p>
        </div>
    </div>
</div>

{{-- PARTICIPATING SCHOOLS --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold mb-3">Participating Schools</h3>

    <div class="flex flex-wrap gap-2">
        @foreach($event->schools as $school)
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                {{ $school->name }}
            </span>
        @endforeach
    </div>
</div>

{{-- 🏅 WINNERS --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">
        Medal Winners
    </h3>

    <form method="POST" action="{{ route('events.winners', $event->id) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @csrf

        @foreach(['Gold','Silver','Bronze'] as $medal)
            @php
                $field = strtolower($medal).'_school_id';
            @endphp

            <div>
                <label class="block font-medium text-{{ strtolower($medal) == 'gold' ? 'yellow' : (strtolower($medal) == 'silver' ? 'gray' : 'amber') }}-700">
                    {{ $medal }} Medal
                </label>
                <select name="{{ $field }}" required class="w-full border rounded px-3 py-2">
                    <option value="">Select School</option>
                    @foreach($event->schools as $school)
                        <option value="{{ $school->id }}"
                            {{ old($field, $event->$field) == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endforeach

        <div class="md:col-span-3 flex justify-end mt-4">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded font-semibold">
                Save Winners
            </button>
        </div>
    </form>
</div>

@endsection
