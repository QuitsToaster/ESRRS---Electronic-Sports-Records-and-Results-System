@extends('layouts.app')

@section('page-title', 'Edit Athlete')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the athlete's information by modifying the details below.
    </p>
</div>
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('athletes.update', $athlete->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $athlete->name) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-3">
            <label class="block mb-1">Team</label>
            <select name="school_id" class="w-full border rounded px-3 py-2">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $athlete->school_id==$school->id ? 'selected':'' }}>{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Sport</label>
            <select name="sport_id" class="w-full border rounded px-3 py-2">
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ $athlete->sport_id==$sport->id ? 'selected':'' }}>{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Division</label>
            <select name="division" class="w-full border rounded px-3 py-2">
                <option value="Elementary" {{ $athlete->division=='Elementary'?'selected':'' }}>Elementary</option>
                <option value="Secondary" {{ $athlete->division=='Secondary'?'selected':'' }}>Secondary</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Category</label>
            <select name="category" class="w-full border rounded px-3 py-2">
                <option value="Boys" {{ $athlete->category=='Boys'?'selected':'' }}>Boys</option>
                <option value="Girls" {{ $athlete->category=='Girls'?'selected':'' }}>Girls</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Active" {{ $athlete->status=='Active'?'selected':'' }}>Active</option>
                <option value="Injured" {{ $athlete->status=='Injured'?'selected':'' }}>Injured</option>
                <option value="Disqualified" {{ $athlete->status=='Disqualified'?'selected':'' }}>Disqualified</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Update Athlete</button>
    </form>
</div>
@endsection
