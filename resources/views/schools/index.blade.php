@extends('layouts.app')

@section('page-title', 'Schools')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Participating Schools</h1>
    <a href="{{ route('schools.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add School
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
                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Logo</th>
                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">School Name</th>
                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Division</th>
                <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-200">Province/City</th>
                <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-200">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schools as $school)
                <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                    {{-- Logo --}}
                    <td class="px-4 py-2">
                        @if($school->logo)
                            <img src="{{ asset('storage/'.$school->logo) }}" 
                                 alt="{{ $school->name }}" 
                                 class="w-16 h-16 object-cover rounded border border-gray-300 dark:border-gray-600">
                        @else
                            <span class="text-gray-500">No Logo</span>
                        @endif
                    </td>

                    {{-- School Info --}}
                    <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-200">{{ $school->name }}</td>
                    <td class="px-4 py-2">{{ $school->division }}</td>
                    <td class="px-4 py-2">{{ $school->province ?? '-' }}/{{ $school->city ?? '-' }}</td>

                    {{-- Actions --}}
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('schools.edit', $school->id) }}" 
                           class="text-blue-600 hover:text-blue-800 font-semibold mr-2">Edit</a>
                        <form action="{{ route('schools.destroy', $school->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this school?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">No schools found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
