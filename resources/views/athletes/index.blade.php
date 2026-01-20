@extends('layouts.app')

@section('page-title', 'Athletes')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Athletes</h1>
    <a href="{{ route('athletes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Athlete
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 overflow-x-auto">
    <table class="min-w-full border border-gray-200 dark:border-gray-700">
        <thead class="bg-blue-200 dark:bg-blue-900">
            <tr>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">School</th>
                <th class="px-4 py-2 text-left">Sport</th>
                <th class="px-4 py-2 text-left">Division</th>
                <th class="px-4 py-2 text-left">Category</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($athletes as $athlete)
                <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                    <td class="px-4 py-2">{{ $athlete->name }}</td>
                    <td class="px-4 py-2">{{ $athlete->school->name }}</td>
                    <td class="px-4 py-2">{{ $athlete->sport->name }}</td>
                    <td class="px-4 py-2">{{ $athlete->division }}</td>
                    <td class="px-4 py-2">{{ $athlete->category }}</td>
                    <td class="px-4 py-2">{{ $athlete->status }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('athletes.edit', $athlete->id) }}" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                        <form action="{{ route('athletes.destroy', $athlete->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this athlete?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-2 text-center text-gray-500">No athletes found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
