@extends('layouts.app')

@section('page-title', 'Sports')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of sports available in the system.
        </p>
    </div>
    <a href="{{ route('sports.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
        + Add Sport
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Sport Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                    Type of Game
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($sports as $sport)
                <tr class="hover:bg-gray-50 transition">
                    <!-- Sport Name -->
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $sport->name }}
                    </td>

                    <!-- Type of Game -->
                    <td class="px-6 py-4">
                        @if($sport->types->count())
                            <div class="flex flex-wrap gap-2">
                                @foreach($sport->types as $type)
                                    <span class="px-3 py-1 text-xs font-medium
                                                 bg-blue-100 text-blue-700
                                                 rounded-full">
                                        {{ $type->type_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-gray-400 italic">
                                No type added
                            </span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('sports.edit', $sport->id) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium mr-3">
                            Edit
                        </a>

                        <form action="{{ route('sports.destroy', $sport->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this sport?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800 font-medium">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3"
                        class="px-6 py-6 text-center text-gray-500">
                        No sports found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
