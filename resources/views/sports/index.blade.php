@extends('layouts.app')

@section('page-title', 'Sports')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of sports available in the system.
        </p>
    </div>
    <a href="{{ route('sports.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Sport
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-6 py-3 text-left font-medium text-gray-700 uppercase">Name</th>
                <th class="px-6 py-3 text-right font-medium text-gray-700 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sports as $sport)
                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-6 py-4">{{ $sport->name }}</td>
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
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No sports found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
