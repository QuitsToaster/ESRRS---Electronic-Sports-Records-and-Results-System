@extends('layouts.app')

@section('page-title', 'Award Medal')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Award a medal to a school or athlete for a specific event and sport.
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

    <form action="{{ route('results.store') }}" method="POST">
        @csrf

        {{-- Event --}}
        <div class="mb-3">
            <label class="font-semibold">Event</label>
            <select name="event_id" class="w-full border rounded px-3 py-2" required>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Sport --}}
        <div class="mb-3">
            <label class="font-semibold">Sport</label>
            <select name="sport_id" class="w-full border rounded px-3 py-2" required>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- School --}}
        <div class="mb-3">
            <label class="font-semibold">School</label>
            <select name="school_id" class="w-full border rounded px-3 py-2" required>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Athlete --}}
        <div class="mb-3">
            <label class="font-semibold">Athlete (optional)</label>
            <select name="athlete_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Select Athlete --</option>
                @foreach($athletes as $athlete)
    <option value="{{ $athlete->id }}">
        {{ $athlete->full_name }}
    </option>
@endforeach

            </select>
        </div>

        {{-- Division --}}
        <div class="mb-3">
            <label class="font-semibold">Division</label>
            <select name="division" class="w-full border rounded px-3 py-2">
                <option>Elementary</option>
                <option>Secondary</option>
            </select>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="font-semibold">Category</label>
            <select name="category" class="w-full border rounded px-3 py-2">
                <option>Boys</option>
                <option>Girls</option>
                <option>Mixed</option>
            </select>
        </div>

        {{-- Medal --}}
        <div class="mb-4">
            <label class="font-semibold">Medal</label>
            <select name="medal" class="w-full border rounded px-3 py-2">
                <option>Gold</option>
                <option>Silver</option>
                <option>Bronze</option>
            </select>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('results.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Award Medal</button>
        </div>
    </form>
</div>
@endsection
