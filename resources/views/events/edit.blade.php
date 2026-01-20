@extends('layouts.app')

@section('page-title', 'Add Event')

@section('content')
<div class="bg-white shadow rounded-lg p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Add New Event</h1>
    <p class="text-sm text-gray-600 mb-6">Fill in the event details below.</p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.store') }}" method="POST" class="space-y-4 w-full">
        @csrf

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Event Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Sport</label>
            <select name="sport_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select Sport</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Level</label>
                <select name="level" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Level</option>
                    <option value="Division">Division</option>
                    <option value="Regional">Regional</option>
                    <option value="National">National</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Age Group</label>
                <select name="age_group" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Age Group</option>
                    <option value="Elementary">Elementary</option>
                    <option value="Secondary">Secondary</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Category</label>
                <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    <option value="Boys">Boys</option>
                    <option value="Girls">Girls</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Venue</label>
                <input type="text" name="venue" value="{{ old('venue') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
        </div>

        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded shadow text-gray-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">Save</button>
        </div>
    </form>
</div>
@endsection
