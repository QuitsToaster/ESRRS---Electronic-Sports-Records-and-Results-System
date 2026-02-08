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

{{-- PARTICIPATING DIVISIONS --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold mb-3">
        Participating Divisions (SDOs)
    </h3>

    <div class="flex flex-wrap gap-2">
        @forelse($event->divisions as $division)
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                {{ $division->name }}
            </span>
        @empty
            <p class="text-sm text-gray-500">No divisions assigned to this event.</p>
        @endforelse
    </div>
</div>

{{-- EVENT STATUS MANAGEMENT --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Update Event Status</h3>

    <form method="POST" action="{{ route('events.updateStatus', $event->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf

        <div>
            <label class="block font-medium mb-1">Status</label>
            <select name="status" id="statusSelect" class="w-full border rounded px-3 py-2" required>
                @foreach(['Upcoming','Ongoing','Delayed','Finished'] as $status)
                    <option value="{{ $status }}" {{ $event->status === $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="reasonDiv" class="{{ $event->status !== 'Delayed' ? 'hidden' : '' }}">
            <label class="block font-medium mb-1">Reason for Delay</label>
            <textarea name="status_reason" rows="3" class="w-full border rounded px-3 py-2" placeholder="Provide reason for delay">{{ $event->status_reason }}</textarea>
        </div>

        <div class="md:col-span-2 flex justify-end mt-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-semibold">
                Save Status
            </button>
        </div>
    </form>
</div>

{{-- Add JS to toggle reason field --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('statusSelect');
    const reasonDiv = document.getElementById('reasonDiv');

    statusSelect.addEventListener('change', function () {
        if (this.value === 'Delayed') {
            reasonDiv.classList.remove('hidden');
        } else {
            reasonDiv.classList.add('hidden');
        }
    });
});
</script>


{{-- 🏅 MEDAL WINNERS --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">
        Medal Winners
    </h3>

    <form method="POST"
          action="{{ route('events.winners', $event->id) }}"
          class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @csrf

        @foreach(['Gold','Silver','Bronze'] as $medal)
    @php $field = strtolower($medal) . '_division_id'; @endphp

    <div>
        <label class="block font-medium mb-1
            {{ strtolower($medal) === 'gold' ? 'text-yellow-700' :
               (strtolower($medal) === 'silver' ? 'text-gray-700' : 'text-amber-700') }}">
            {{ $medal }} Medal
        </label>

        <select name="{{ $field }}" required
                class="w-full border rounded px-3 py-2">
            <option value="">Select Division (SDO)</option>
            @foreach($event->divisions as $division)
                <option value="{{ $division->id }}"
                    {{ old($field, $event->$field) == $division->id ? 'selected' : '' }}>
                    {{ $division->name }}
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
