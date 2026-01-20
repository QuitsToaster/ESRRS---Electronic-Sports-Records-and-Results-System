@extends('layouts.app')

@section('page-title', 'Sports')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Sports Categories</h1>
    <a href="{{ route('sports.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Sport
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
                <th class="px-6 py-3 text-left font-medium text-gray-700 dark:text-gray-200 uppercase">ID</th>
                <th class="px-6 py-3 text-left font-medium text-gray-700 dark:text-gray-200 uppercase">Name</th>
                <th class="px-6 py-3 text-right font-medium text-gray-700 dark:text-gray-200 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sports as $sport)
                <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $sport->id }}</td>
                    <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $sport->name }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('sports.edit', $sport->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold mr-2">Edit</a>
                        <form action="{{ route('sports.destroy', $sport->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this sport?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No sports found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
    