@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

<form method="GET" class="flex flex-wrap gap-4 mb-6">
    <div>
        <label>Division</label>
        <select name="division" onchange="this.form.submit()">
            @foreach(['Elementary','Secondary'] as $d)
                <option value="{{ $d }}" {{ $division === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Category</label>
        <select name="category" onchange="this.form.submit()">
            @foreach(['Boys','Girls','Mixed'] as $c)
                <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
</form>

<form action="{{ route('results_divisions.store') }}" method="POST">
@csrf
<input type="hidden" name="division" value="{{ $division }}">
<input type="hidden" name="category" value="{{ $category }}">

<table class="min-w-full border-collapse text-sm">
<thead class="bg-gray-100">
<tr>
    <th>Sport / Event</th>
    @foreach($divisions as $div)
        <th colspan="3" class="text-center">
            {{ $div->name }}
            <div class="grid grid-cols-3 text-xs">
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
    <td colspan="{{ 1 + ($divisions->count()*3) }}">{{ strtoupper($sport->name) }}</td>
</tr>
@foreach($sport->types as $type)
<tr>
    <td class="italic text-gray-600">{{ $type->type_name }}</td>
    @foreach($divisions as $div)
        @foreach(['Gold','Silver','Bronze'] as $medal)
            @php
                $key = $sport->id.'-'.($type->id ?? 0).'-'.$div->name.'-'.$medal;
                $value = $existingResults->get($key) ? $existingResults->get($key)->count() : 0;
            @endphp
            <td>
                <input type="number" min="0"
                    name="results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $div->name }}][{{ $medal }}]"
                    value="{{ $value }}"
                    class="w-14 text-center border rounded">
            </td>
        @endforeach
    @endforeach
</tr>
@endforeach
@endforeach
</tbody>
</table>

<div class="flex justify-end mt-4 gap-2">
    <a href="{{ route('results_divisions.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Save Results</button>
</div>

</form>
@endsection
