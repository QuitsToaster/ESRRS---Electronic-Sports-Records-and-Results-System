@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

{{-- FILTER BAR --}}
<form method="GET" class="flex flex-wrap gap-4 mb-6">
    <div>
        <label class="text-sm font-semibold">Division</label>
        <select name="division" onchange="this.form.submit()"
            class="border rounded px-3 py-2">
            <option value="Elementary" {{ $division == 'Elementary' ? 'selected' : '' }}>Elementary</option>
            <option value="Secondary" {{ $division == 'Secondary' ? 'selected' : '' }}>Secondary</option>
        </select>
    </div>

    <div>
        <label class="text-sm font-semibold">Category</label>
        <select name="category" onchange="this.form.submit()"
            class="border rounded px-3 py-2">
            <option value="Boys" {{ $category == 'Boys' ? 'selected' : '' }}>Boys</option>
            <option value="Girls" {{ $category == 'Girls' ? 'selected' : '' }}>Girls</option>
            <option value="Mixed" {{ $category == 'Mixed' ? 'selected' : '' }}>Mixed</option>
        </select>
    </div>
</form>

<form action="{{ route('results.store') }}" method="POST">
@csrf

{{-- IMPORTANT: PASS FILTER VALUES --}}
<input type="hidden" name="division" value="{{ $division }}">
<input type="hidden" name="category" value="{{ $category }}">

<div class="bg-white border rounded-lg shadow overflow-x-auto">
<table class="min-w-full border-collapse text-sm">

    {{-- HEADER --}}
    <thead class="bg-gray-100">
        <tr>
            <th class="border px-4 py-3 text-left w-64">Sport / Event</th>

            @foreach($schools as $school)
                <th colspan="3" class="border px-4 py-2 text-center">
                    {{ $school->name }}
                    <div class="grid grid-cols-3 gap-2 text-xs mt-1">
                        <span class="text-yellow-600 font-semibold">G</span>
                        <span class="text-gray-500 font-semibold">S</span>
                        <span class="text-orange-600 font-semibold">B</span>
                    </div>
                </th>
            @endforeach
        </tr>
    </thead>

    {{-- BODY --}}
    <tbody>
        @forelse($sports as $sport)

            {{-- SPORT ROW --}}
            <tr class="bg-gray-50 font-bold">
                <td class="border px-4 py-2" colspan="{{ 1 + ($schools->count() * 3) }}">
                    {{ strtoupper($sport->name) }}
                </td>
            </tr>

            {{-- SPORT TYPES --}}
            @forelse($sport->types as $type)
                <tr>
                    <td class="border px-6 py-2">
                        {{ $type->type_name }}
                    </td>

                    @foreach($schools as $school)
                        @foreach(['Gold','Silver','Bronze'] as $medal)
                            <td class="border px-2 py-1 text-center">
                                <input type="number"
                                    min="0"
                                    name="results[{{ $sport->id }}][{{ $type->id }}][{{ $school->id }}][{{ $medal }}]"
                                    class="w-14 border rounded px-1 py-1 text-center focus:ring focus:ring-blue-200">
                            </td>
                        @endforeach
                    @endforeach
                </tr>
            @empty
                {{-- SPORT WITH NO TYPES --}}
                <tr>
                    <td class="border px-6 py-2 italic text-gray-500">
                        No event type
                    </td>

                   @foreach($schools as $school)
                        @foreach(['Gold','Silver','Bronze'] as $medal)
                            @php
                                $key = $sport->id . '-'. (null) . '-' . $school->id . '-' . $medal;
                                $existingCount = isset($existingResults[$key]) ? $existingResults[$key]->count() : 0;
                            @endphp
                            <td class="border px-2 py-1 text-center">
                                <input type="number"
                                    min="0"
                                    name="results[{{ $sport->id }}][0][{{ $school->id }}][{{ $medal }}]"
                                    value="{{ old('results.' . $sport->id . '.0.' . $school->id . '.' . $medal, $existingCount) }}"
                                    class="w-14 border rounded px-1 py-1 text-center focus:ring focus:ring-blue-200">
                            </td>
                        @endforeach
                    @endforeach
                </tr>
            @endforelse

        @empty
            <tr>
                <td colspan="{{ 1 + ($schools->count() * 3) }}"
                    class="text-center py-6 text-gray-500">
                    No sports found for this division and category.
                </td>
            </tr>
        @endforelse
    </tbody>

</table>
</div>

<div class="flex justify-end mt-6 gap-3">
    <a href="{{ route('results.index') }}" class="px-4 py-2 bg-gray-200 rounded">
        Cancel
    </a>
    <button class="px-6 py-2 bg-blue-600 text-white rounded shadow">
        Save Results
    </button>
</div>

</form>
@endsection
