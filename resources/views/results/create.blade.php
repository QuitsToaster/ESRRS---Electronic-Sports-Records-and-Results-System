@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

{{-- FILTER BAR --}}
<form method="GET" class="flex flex-wrap gap-4 mb-6">

    {{-- Division Filter --}}
    <div>
        <label class="text-sm font-semibold">Division</label>
        <select name="division" onchange="this.form.submit()" class="border rounded px-3 py-2">
            @foreach(['Elementary','Secondary'] as $d)
                <option value="{{ $d }}" {{ request('division', 'Elementary') == $d ? 'selected' : '' }}>
                    {{ $d }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Category Filter --}}
    <div>
        <label class="text-sm font-semibold">Category</label>
        <select name="category" onchange="this.form.submit()" class="border rounded px-3 py-2">
            @foreach(['Boys','Girls','Mixed'] as $c)
                <option value="{{ $c }}" {{ request('category', 'Boys') == $c ? 'selected' : '' }}>
                    {{ $c }}
                </option>
            @endforeach
        </select>
    </div>

</form>


<form action="{{ route('results.store') }}" method="POST">
@csrf

<input type="hidden" name="division" value="{{ $division }}">
<input type="hidden" name="category" value="{{ $category }}">

<div class="bg-white border rounded-lg shadow overflow-x-auto">
<table class="min-w-full border-collapse text-sm">

<thead class="bg-gray-100">
<tr>
    <th class="border px-4 py-3 w-64">Sport / Event</th>

    @foreach($divisions as $div)
        <th colspan="3" class="border px-4 py-2 text-center">
            {{ $div->name }}
            <div class="grid grid-cols-3 text-xs mt-1">
                <span class="text-yellow-600">G</span>
                <span class="text-gray-500">S</span>
                <span class="text-orange-600">B</span>
            </div>
        </th>
    @endforeach
</tr>
</thead>

<tbody>

@foreach($sports as $sport)

<tr class="bg-gray-50 font-bold">
    <td class="border px-4 py-2" colspan="{{ 1 + ($divisions->count() * 3) }}">
        {{ strtoupper($sport->name) }}
    </td>
</tr>

@foreach($sport->types as $type)
<tr>
    <td class="border px-6 py-2 italic text-gray-600">{{ $type->type_name }}</td>

    @foreach($divisions as $div)
        @foreach(['Gold','Silver','Bronze'] as $medal)

            @php
                $key = $sport->id . '-' . $type->id . '-' . $div->id . '-' . $medal;
                $existingCount = $existingResults->get($key);
                $value = $existingCount ? $existingCount->count() : ($eventResults[$key] ?? 0);
            @endphp

            <td class="border px-2 py-1 text-center">
                <input type="number"
                    min="0"
                    name="results[{{ $sport->id }}][{{ $type->id }}][{{ $div->id }}][{{ $medal }}]"
                    value="{{ $value }}"
                    class="w-14 border rounded px-1 py-1 text-center focus:ring">
            </td>

        @endforeach
    @endforeach
</tr>
@endforeach

@endforeach

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
