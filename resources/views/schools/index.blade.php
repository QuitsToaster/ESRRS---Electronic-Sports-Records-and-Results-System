@extends('layouts.app')

@section('page-title', 'Schools')

@section('content')

{{-- Header with Add Button --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of schools registered in the system
        </p>
    </div>

    <a href="{{ route('schools.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        + Add School
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Search Bar --}}
<form method="GET" action="{{ route('schools.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search schools..."
           class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
        Search
    </button>

    @if(request('search'))
        <a href="{{ route('schools.index') }}"
           class="text-gray-500 hover:text-gray-700 ml-2">
            Clear
        </a>
    @endif
</form>

{{-- Empty State --}}
@if($schools->count() === 0)
    <div class="bg-white border border-gray-300 rounded-lg p-8 text-center text-gray-500 shadow-sm">
        No schools found.
    </div>
@else

{{-- Schools Table --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto">

    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Logo</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">School Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Division</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Address</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">School Code</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @foreach($schools as $school)
                <tr class="hover:bg-gray-50 transition">

                    {{-- Logo --}}
                    <td class="px-4 py-3">
                        @if($school->logo)
                            <img src="{{ asset('storage/'.$school->logo) }}"
                                 alt="{{ $school->name }}"
                                 class="w-14 h-14 object-cover rounded border border-gray-300">
                        @else
                            <span class="text-sm text-gray-500">No Logo</span>
                        @endif
                    </td>

                    {{-- Name --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $school->name }}
                    </td>

                    {{-- Type --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $school->school_type ?? '-' }}
                    </td>

                    {{-- Division --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $school->division ?? '-' }}
                    </td>

                    {{-- Address --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $school->school_address ?? '-' }}
                    </td>

                    {{-- School Code --}}
                    <td class="px-4 py-3 text-gray-700">
                        {{ $school->school_code ?? '-' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end items-center gap-4 text-sm font-medium">
                            <a href="{{ route('schools.edit', $school->id) }}"
                               class="text-yellow-600 hover:text-yellow-800">
                                Edit
                            </a>

                            <form action="{{ route('schools.destroy', $school->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this school?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ✅ PAGINATION --}}
<div class="mt-6">
    {{ $schools->links() }}
</div>

@endif

@endsection
