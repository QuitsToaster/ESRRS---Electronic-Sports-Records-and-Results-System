@extends('layouts.app')

@section('page-title', 'Edit Event')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Edit Event</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('events.update', $event->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Event Name</label>
                <input type="text" name="name" value="{{ old('name', $event->name) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
            </div>

            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Sport</label>
                <select name="sport_id" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                    <option value="">Select Sport</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ $event->sport_id == $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Level</label>
                    <select name="level" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                        <option value="">Select Level</option>
                        <option value="Division" {{ $event->level=='Division'?'selected':'' }}>Division</option>
                        <option value="Regional" {{ $event->level=='Regional'?'selected':'' }}>Regional</option>
                        <option value="National" {{ $event->level=='National'?'selected':'' }}>National</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Age Group</label>
                    <select name="age_group" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                        <option value="">Select Age Group</option>
                        <option value="Elementary" {{ $event->age_group=='Elementary'?'selected':'' }}>Elementary</option>
                        <option value="Secondary" {{ $event->age_group=='Secondary'?'selected':'' }}>Secondary</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Category</label>
                    <select name="category" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                        <option value="">Select Category</option>
                        <option value="Boys" {{ $event->category=='Boys'?'selected':'' }}>Boys</option>
                        <option value="Girls" {{ $event->category=='Girls'?'selected':'' }}>Girls</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <a href="{{ route('events.index') }}" class="mr-2 px-4 py-2 rounded bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600">Cancel</a>
            <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Update</button>
        </div>
    </form>
</div>
@endsection
