@extends('layouts.app')

@section('page-title', 'Add Athlete')

@section('content')

<p class="text-sm text-gray-600 mb-4">
    Add a new athlete to the system. Profile photo is optional.
</p>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

<form action="{{ route('athletes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Profile Photo --}}
    <div class="mb-4">
        <label class="block mb-1 font-medium">Profile Photo</label>
        <input type="file"
               name="profile_photo"
               accept="image/*"
               class="w-full border rounded px-3 py-2">
        <p class="text-xs text-gray-500 mt-1">
            JPG or PNG, max 2MB
        </p>
    </div>

    <div class="mb-3">
        <label class="block mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div class="mb-3">
        <label class="block mb-1">Team</label>
        <select name="school_id" class="w-full border rounded px-3 py-2">
            @foreach($schools as $school)
                <option value="{{ $school->id }}">{{ $school->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="block mb-1">Sport</label>
        <select name="sport_id" class="w-full border rounded px-3 py-2">
            @foreach($sports as $sport)
                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="block mb-1">Division</label>
        <select name="division" class="w-full border rounded px-3 py-2">
            <option value="Elementary">Elementary</option>
            <option value="Secondary">Secondary</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="block mb-1">Category</label>
        <select name="category" class="w-full border rounded px-3 py-2">
            <option value="Boys">Boys</option>
            <option value="Girls">Girls</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="block mb-1">Status</label>
        <select name="status" class="w-full border rounded px-3 py-2">
            <option value="Active">Active</option>
            <option value="Injured">Injured</option>
            <option value="Disqualified">Disqualified</option>
        </select>
    </div>

    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
        Add Athlete
    </button>
</form>

</div>
@endsection
