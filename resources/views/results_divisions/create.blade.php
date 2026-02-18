@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\Division;
use App\Models\Athlete;
use App\Models\AllAwardResult;

$tabs = [
    'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
    'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
    'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
    'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
    'Para Games'        => ['division' => 'Paragames', 'category' => null],
];

$divisions = Division::orderBy('name')->get();
$athletes  = Athlete::orderBy('last_name')->get();

/* ================= PREFILL RESULTS ================= */
$prefillResults = [];

foreach ($tabs as $tabName => $tab) {
    $query = AllAwardResult::where('division', $tab['division']);

    if (!is_null($tab['category'])) {
        $query->where('category', $tab['category']);
    } else {
        $query->whereNull('category');
    }

    foreach ($query->get() as $r) {
        $prefillResults[$tabName]
            [$r->sport_id]
            [$r->type_id ?? 0]
            [$r->sdo]
            [$r->medal] = $r->count;
    }
}
@endphp

<div x-data="awardApp()" class="p-4 space-y-6">

{{-- ================= AWARD TYPE ================= --}}
<select x-model="awardType" class="border rounded px-3 py-2 w-64 mb-4">
    <option value="all">All Awards</option>
    <option value="individual">Individual Awards</option>
</select>

{{-- ================= TABS ================= --}}
<ul class="flex border-b mb-6">
@foreach($tabs as $name => $tab)
<li>
<button @click="activeTab='{{ $name }}'"
        class="px-4 py-2 border-b-2 font-semibold"
        :class="activeTab==='{{ $name }}'
            ? 'border-blue-600 text-blue-600'
            : 'text-gray-500'">
    {{ $name }}
</button>
</li>
@endforeach
</ul>

@foreach($tabs as $tabName => $tab)
@php
$sports = Sport::with('types')
    ->where('division', $tab['division'])
    ->orderBy('name')
    ->get();
@endphp

<div x-show="activeTab==='{{ $tabName }}'" x-cloak>

<h2 class="text-xl font-bold mb-4">{{ $tabName }}</h2>

{{-- ========================================================= --}}
{{-- 🏆 TOP MEDAL SUMMARY TABLE (FIXED & RESTORED) --}}
{{-- ========================================================= --}}
<div x-show="awardType==='all'" class="overflow-x-auto mb-8">
<table class="w-full border border-collapse text-sm">
<thead>
<tr class="bg-gray-200 text-center">
    <th rowspan="2" class="border px-3">SPORT</th>
    @foreach($divisions as $d)
        <th colspan="3" class="border">{{ $d->name }}</th>
    @endforeach
</tr>
<tr>
@foreach($divisions as $d)
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
@foreach($divisions as $d)
<td class="border text-center" x-text="getTotal('{{ $tabName }}','{{ $sport->id }}','{{ $d->name }}','Gold')"></td>
<td class="border text-center" x-text="getTotal('{{ $tabName }}','{{ $sport->id }}','{{ $d->name }}','Silver')"></td>
<td class="border text-center" x-text="getTotal('{{ $tabName }}','{{ $sport->id }}','{{ $d->name }}','Bronze')"></td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
</div>

{{-- ========================================================= --}}
{{-- 📝 ALL AWARDS FORM (FIXED) --}}
{{-- ========================================================= --}}
<form x-show="awardType==='all'" method="POST" action="{{ route('results_all_awards.store') }}">
@csrf
<input type="hidden" name="division" value="{{ $tab['division'] }}">
@if(!is_null($tab['category']))
<input type="hidden" name="category" value="{{ $tab['category'] }}">
@endif

@foreach($sports as $sport)
<h3 class="font-semibold mt-6">{{ strtoupper($sport->name) }}</h3>

<table class="w-full border text-sm mt-2">
<thead class="bg-gray-200 text-center">
<tr>
    <th>Event</th>
    @foreach($divisions as $d)
        <th colspan="3">{{ $d->name }}</th>
    @endforeach
</tr>
<tr>
<th></th>
@foreach($divisions as $d)
<th>G</th><th>S</th><th>B</th>
@endforeach
</tr>
</thead>

<tbody>
@foreach($sport->types as $type)
<tr>
<td class="border px-2">{{ $type->type_name }}</td>

@foreach($divisions as $d)
@foreach(['Gold','Silver','Bronze'] as $medal)
<td class="border text-center">
<input type="number" min="0" class="w-16 border rounded text-center"
x-model.number="results['{{ $tabName }}']['{{ $sport->id }}']['{{ $type->id }}']['{{ $d->name }}']['{{ $medal }}']"
name="results[{{ $sport->id }}][{{ $type->id }}][{{ $d->name }}][{{ $medal }}]">
</td>
@endforeach
@endforeach
</tr>
@endforeach
</tbody>
</table>
@endforeach

<div class="flex justify-end mt-6">
<button class="bg-blue-600 text-white px-6 py-2 rounded">Save All Awards</button>
</div>
</form>

</div>
@endforeach
</div>

<script>
function awardApp(){
return {
    activeTab:'{{ array_key_first($tabs) }}',
    awardType:'all',
    results:@json($prefillResults),

    getTotal(tab, sportId, sdo, medal){
        let sum = 0;
        const sport = this.results[tab]?.[sportId] || {};
        for (let typeId in sport) {
            sum += sport[typeId]?.[sdo]?.[medal] || 0;
        }
        return sum;
    }
}
}
</script>

@endsection
