@extends('layouts.app')

@section('page-title', 'Events')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">CAVRAA Events</h1>
    <a href="{{ route('events.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Event
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if($events->isEmpty())
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center text-gray-500 dark:text-gray-300">
        No events found.
    </div>
@else
    <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <table class="min-w-full border border-gray-200 dark:border-gray-700">
            <thead class="bg-blue-200 dark:bg-blue-900">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Event Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Sport</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Level</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Age Group</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Category</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Start Date</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">End Date</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-200">Venue</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-700 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->sport->name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->level }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->age_group }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->category }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->start_date->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->end_date->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $event->venue }}</td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('events.edit', $event->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold mr-2">Edit</a>
                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
