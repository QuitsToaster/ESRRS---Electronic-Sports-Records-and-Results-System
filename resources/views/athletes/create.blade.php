@extends('layouts.app')

@section('page-title', 'Add Athlete')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-w-lg">
    <h1 class="text-xl font-bold mb-4">Add Athlete</h1>

    <form action="{{ route('athletes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-3">
            <label class="block mb-1">School</label>
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

        <div class="mb-3">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Active">Active</option>
                <option value="Injured">Injured</option>
                <option value="Disqualified">Disqualified</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Add Athlete</button>
    </form>
</div>
@endsection
