@extends('layouts.app')

@section('page-title', 'Coaches')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of coaches registered in the system
        </p>
    </div>
    <a href="{{ route('coaches.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Coach
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
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">School</th>
                <th class="px-4 py-2 text-left">Sport</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($coaches as $coach)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-2">{{ $coach->name }}</td>
                    <td class="px-4 py-2">{{ $coach->school->name }}</td>
                    <td class="px-4 py-2">{{ $coach->sport->name }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('coaches.edit', $coach->id) }}" class="text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                        <form action="{{ route('coaches.destroy', $coach->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this coach?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No coaches found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
