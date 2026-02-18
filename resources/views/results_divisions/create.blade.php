@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\Division;
use App\Models\Athlete;

$tabs = [
    'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
    'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
    'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
    'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
    'Para Games'        => ['division' => 'Paragames', 'category' => null],
];

$divisions = Division::orderBy('name')->get();
@endphp

<div x-data="{
        activeTab: '{{ Str::slug(array_key_first($tabs)) }}',
        awardType: 'individual',
        results: {},
        getMedalCount(sportId, typeId, sdo, medal) {
            return parseInt(this.results?.[sportId]?.[typeId]?.[sdo]?.[medal] || 0);
        },
        sdoTotal(sdo, medal, sportId) {
            let sum = 0;
            Object.keys(this.results[sportId] || {}).forEach(typeId => {
                sum += this.getMedalCount(sportId,typeId,sdo,medal);
            });
            return sum;
        }
    }">

{{-- ================= AWARD TYPE DROPDOWN ================= --}}
<div class="mb-6">
    <label class="block text-sm font-medium mb-1">Award Type</label>
    <select x-model="awardType" class="border rounded px-3 py-2 w-64">
        <option value="all">All Awards</option>
        <option value="individual">Individual Awards</option>
    </select>
</div>

{{-- ================= TABS ================= --}}
<ul class="flex border-b mb-6">
@foreach($tabs as $tabName => $tab)
    <li class="mr-2">
        <button
            @click="activeTab='{{ Str::slug($tabName) }}'"
            :class="activeTab === '{{ Str::slug($tabName) }}'
                ? 'border-blue-600 text-blue-600'
                : 'text-gray-600'"
            class="py-2 px-4 border-b-2 font-semibold"
        >
            {{ $tabName }}
        </button>
    </li>
@endforeach
</ul>

<form action="{{ route('results_divisions.store') }}" method="POST">
@csrf

@foreach($tabs as $tabName => $tab)
@php
    $division = $tab['division'];
    $category = $tab['category'];

    $sports = Sport::with('types')
        ->where(fn($q) =>
            $division === 'Paragames'
                ? $q->where('division','Paragames')
                : $q->where('division','!=','Paragames')
        )
        ->orderBy('name')
        ->get();

    $athletes = []; // We'll not populate this for "All Awards" input
@endphp

<div x-show="activeTab === '{{ Str::slug($tabName) }}'">

<input type="hidden" name="division" value="{{ $division }}">
<input type="hidden" name="category" value="{{ $category }}">

<h2 class="text-2xl font-bold mb-4">{{ $tabName }}</h2>

{{-- ========================================================= --}}
{{-- 🧑 INDIVIDUAL AWARDS (OLD UI) --}}
{{-- ========================================================= --}}
<div x-show="awardType === 'individual'">

@foreach($sports as $sport)
<h3 class="font-bold mt-6">{{ strtoupper($sport->name) }}</h3>

<table class="w-full border text-sm mt-2">
<thead class="bg-gray-100">
<tr>
    <th class="border px-2">Event</th>
    <th class="border px-2">Gold</th>
    <th class="border px-2">Silver</th>
    <th class="border px-2">Bronze</th>
</tr>
</thead>
<tbody>
@foreach($sport->types as $type)
<tr>
<td class="border px-2 italic">{{ $type->type_name }}</td>

@foreach(['Gold','Silver','Bronze'] as $medal)
<td class="border px-2">
    <select
        name="results[{{ $sport->id }}][{{ $type->id }}][{{ $medal }}]"
        class="w-full border rounded px-1"
    >
        <option value="">— Select Athlete —</option>
        @foreach($athletes as $athlete)
            <option value="{{ $athlete->id }}">
                {{ $athlete->last_name }}, {{ $athlete->first_name }}
            </option>
        @endforeach
    </select>
</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
@endforeach

</div>


{{-- ========================================================= --}}
{{-- 🏆 ALL AWARDS (NEW MEDAL TALLY UI) --}}
{{-- ========================================================= --}}
<div x-show="awardType === 'all'">

{{-- ===== TOP MEDAL TALLY WITH SDO LABELS ===== --}}
<div class="overflow-x-auto mb-10">
<table class="w-full border text-sm border-collapse">

<thead>
<tr class="bg-gray-200 text-center">
    <th rowspan="2" class="border px-3">SPORT</th>
    @foreach($divisions as $div)
        <th colspan="3" class="border">{{ $div->name }}<br><span class="text-xs text-gray-600">{{ $div->division ?? '' }}</span></th>
    @endforeach
</tr>
<tr class="text-center">
    @foreach($divisions as $div)
        <th class="border bg-yellow-300">G</th>
        <th class="border bg-gray-300">S</th>
        <th class="border bg-orange-200">B</th>
    @endforeach
</tr>
</thead>

<tbody>
@foreach($sports as $sport)
<tr>
<td class="border px-3 font-semibold">{{ strtoupper($sport->name) }}</td>

@foreach($divisions as $div)
    <td class="border text-center" x-text="sdoTotal('{{ $div->name }}','Gold','{{ $sport->id }}')">0</td>
    <td class="border text-center" x-text="sdoTotal('{{ $div->name }}','Silver','{{ $sport->id }}')">0</td>
    <td class="border text-center" x-text="sdoTotal('{{ $div->name }}','Bronze','{{ $sport->id }}')">0</td>
@endforeach
</tr>
@endforeach
</tbody>

</table>
</div>

{{-- ===== PER SPORT TABLES WITH MEDAL INPUTS ===== --}}
@foreach($sports as $sport)
<h3 class="font-bold mt-8">{{ strtoupper($sport->name) }}</h3>

<table class="w-full border text-sm mt-2">
<thead class="bg-gray-200 text-center">
<tr>
    <th rowspan="2" class="border">Event</th>
    @foreach($divisions as $div)
        <th colspan="3" class="border">{{ $div->name }}<br><span class="text-xs text-gray-600">{{ $div->division ?? '' }}</span></th>
    @endforeach
</tr>
<tr class="text-center">
    @foreach($divisions as $div)
        <th class="border bg-yellow-300">G</th>
        <th class="border bg-gray-300">S</th>
        <th class="border bg-orange-200">B</th>
    @endforeach
</tr>
</thead>

<tbody>
@foreach($sport->types as $type)
<tr>
<td class="border px-2">{{ $type->type_name }}</td>

@foreach($divisions as $div)
<td class="border text-center">
    <input type="number" min="0" class="w-16 text-center border rounded px-1"
        x-model.number="results['{{ $sport->id }}'] = results['{{ $sport->id }}'] || {}; 
                        results['{{ $sport->id }}']['{{ $type->id }}'] = results['{{ $sport->id }}']['{{ $type->id }}'] || {}; 
                        results['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}'] = results['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}'] || {};
                        results['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}']['Gold']"
        name="results[{{ $sport->id }}][{{ $type->id }}][{{ $div->name }}][Gold]"
    >
</td>
<td class="border text-center">
    <input type="number" min="0" class="w-16 text-center border rounded px-1"
        x-model.number="results['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}']['Silver']"
        name="results[{{ $sport->id }}][{{ $type->id }}][{{ $div->name }}][Silver]"
    >
</td>
<td class="border text-center">
    <input type="number" min="0" class="w-16 text-center border rounded px-1"
        x-model.number="results['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}']['Bronze']"
        name="results[{{ $sport->id }}][{{ $type->id }}][{{ $div->name }}][Bronze]"
    >
</td>
@endforeach
</tr>
@endforeach
</tbody>

{{-- ===== FOOTER TOTAL ROW ===== --}}
<tfoot>
<tr class="bg-green-100 font-semibold text-center">
    <td class="border px-2">TOTAL</td>
    @foreach($divisions as $div)
        <td class="border" x-text="Object.keys(results['{{ $sport->id }}'] || {}).reduce((sum,typeId)=>sum + getMedalCount('{{ $sport->id }}',typeId,'{{ $div->name }}','Gold'),0)">0</td>
        <td class="border" x-text="Object.keys(results['{{ $sport->id }}'] || {}).reduce((sum,typeId)=>sum + getMedalCount('{{ $sport->id }}',typeId,'{{ $div->name }}','Silver'),0)">0</td>
        <td class="border" x-text="Object.keys(results['{{ $sport->id }}'] || {}).reduce((sum,typeId)=>sum + getMedalCount('{{ $sport->id }}',typeId,'{{ $div->name }}','Bronze'),0)">0</td>
    @endforeach
    <td class="border" x-text="Object.keys(results['{{ $sport->id }}'] || {}).reduce((sum,typeId)=>sum + Object.keys(results['{{ $sport->id }}'][typeId] || {}).reduce((s,sdo)=>s + rowTotal('{{ $sport->id }}',typeId,sdo),0),0)">0</td>
</tr>
</tfoot>

</table>
@endforeach


</div>

</div>
@endforeach

<div class="flex justify-end mt-6">
    <button class="bg-blue-600 text-white px-6 py-2 rounded">
        Save Results
    </button>
</div>

</form>
</div>

@endsection
