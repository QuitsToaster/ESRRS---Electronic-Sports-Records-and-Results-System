@extends('layouts.app')

@section('page-title', 'Edit Coach')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Coach</h1>
    <p class="text-sm text-gray-600 mb-6">
        Update the details of the coach.
    </p>

    <form action="{{ route('coaches.update', $coach->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $coach->name) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-3">
            <label class="block mb-1">School</label>
            <select name="school_id" class="w-full border rounded px-3 py-2">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $coach->school_id==$school->id?'selected':'' }}>{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Sport</label>
            <select name="sport_id" class="w-full border rounded px-3 py-2">
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ $coach->sport_id==$sport->id?'selected':'' }}>{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Update Coach</button>
    </form>
</div>
@endsection
