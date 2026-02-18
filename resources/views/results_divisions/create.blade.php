@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\Division;
use App\Models\AllAwardResult;
use App\Models\AwardRequest;
use App\Models\Event;

$user = auth()->user();
$role = $user->role->name;

$isSecretary  = $role === 'Tournament Secretary';
$isManager    = $role === 'Tournament Manager';
$isSuperAdmin = $role === 'Super Admin';

$tabs = [
    'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
    'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
    'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
    'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
    'Para Games'        => ['division' => 'Paragames', 'category' => null],
];

$divisions = $isSuperAdmin
    ? Division::orderBy('name')->get()
    : Division::where('id', $user->division_id)->get();

$activeEvent = Event::where('is_active', 1)->first();

$pendingRequests = [];
if ($isManager) {
    $pendingRequests = AwardRequest::where('sdo', $user->division->name)
        ->where('status', 'Pending')
        ->with('submitter', 'event')
        ->latest()
        ->get();
}
@endphp

{{-- FLASH MESSAGES --}}
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    {{ session('error') }}
</div>
@endif

{{-- ================= MEDAL TALLY ================= --}}
<div class="overflow-x-auto mb-10">
<table class="w-full border text-sm">
<thead class="bg-gray-200 text-center">
<tr>
    <th class="border p-2">SDO</th>
    <th class="border p-2">Gold</th>
    <th class="border p-2">Silver</th>
    <th class="border p-2">Bronze</th>
    <th class="border p-2">Total</th>
</tr>
</thead>
<tbody>
@foreach($divisions as $division)
@php
$g = AllAwardResult::where('sdo',$division->name)->where('medal','Gold')->sum('count');
$s = AllAwardResult::where('sdo',$division->name)->where('medal','Silver')->sum('count');
$b = AllAwardResult::where('sdo',$division->name)->where('medal','Bronze')->sum('count');
@endphp
<tr class="text-center">
    <td class="border p-2 font-semibold">{{ $division->name }}</td>
    <td class="border p-2">{{ $g }}</td>
    <td class="border p-2">{{ $s }}</td>
    <td class="border p-2">{{ $b }}</td>
    <td class="border p-2 font-bold">{{ $g + $s + $b }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>

<div x-data="awardApp()" class="space-y-6">

{{-- ================= SECRETARY / SUPER ADMIN FORM ================= --}}
@if($isSecretary || $isSuperAdmin)

<form method="POST"
      action="{{ $isSuperAdmin ? route('results_all_awards.store') : route('award_requests.store') }}"
      enctype="multipart/form-data">
@csrf

<input type="hidden" name="event_id" value="{{ $activeEvent?->id }}">
<input type="hidden" name="award_type" value="all">
<input type="hidden" name="division" x-model="currentDivision">
<input type="hidden" name="category" x-model="currentCategory">

@if($isSuperAdmin)
<input type="hidden" name="results" :value="JSON.stringify(results)">
@endif

@if($isSecretary)
<input type="hidden" name="payload" :value="JSON.stringify(results)">
@endif

{{-- TABS --}}
<ul class="flex border-b mb-4">
@foreach($tabs as $name => $tab)
<li>
<button type="button"
    @click="setTab('{{ $name }}','{{ $tab['division'] }}','{{ $tab['category'] }}')"
    class="px-4 py-2 border-b-2"
    :class="activeTab === '{{ $name }}'
        ? 'border-blue-600 text-blue-600'
        : 'text-gray-500'">
    {{ $name }}
</button>
</li>
@endforeach
</ul>

{{-- PER TAB --}}
@foreach($tabs as $tabName => $tab)
@php
$sports = Sport::with('types')->where('division', $tab['division'])->get();
@endphp

<div x-show="activeTab === '{{ $tabName }}'">

@foreach($sports as $sport)
<h3 class="font-semibold mt-6 mb-2">{{ strtoupper($sport->name) }}</h3>

<table class="w-full border text-sm">
<thead class="bg-gray-100 text-center">

{{-- SDO LABEL ROW --}}
<tr>
    <th rowspan="2" class="border p-2">Event</th>
    @foreach($divisions as $d)
        <th colspan="3" class="border p-2 font-semibold">
            {{ $d->name }}
        </th>
    @endforeach
</tr>

{{-- G S B LABEL ROW --}}
<tr>
    @foreach($divisions as $d)
        <th class="border p-1">G</th>
        <th class="border p-1">S</th>
        <th class="border p-1">B</th>
    @endforeach
</tr>

</thead>

<tbody>
@foreach($sport->types as $type)
<tr class="text-center">
    <td class="border px-2 text-left">{{ $type->type_name }}</td>

    @foreach($divisions as $d)
        @foreach(['Gold','Silver','Bronze'] as $m)
        <td class="border">
            <input type="number" min="0"
                class="w-14 text-center border rounded"
                x-model.number="results['{{ $sport->id }}']['{{ $type->id }}']['{{ $d->name }}']['{{ $m }}']">
        </td>
        @endforeach
    @endforeach
</tr>
@endforeach
</tbody>
</table>
@endforeach

</div>
@endforeach

<div class="flex justify-end mt-6">
<button type="submit"
    class="px-6 py-2 rounded text-white {{ $isSuperAdmin ? 'bg-green-600' : 'bg-blue-600' }}">
    {{ $isSuperAdmin ? 'Save Awards' : 'Forward for Validation' }}
</button>
</div>

</form>
@endif

{{-- ================= MANAGER VALIDATION ================= --}}
@if($isManager)
<h2 class="text-lg font-bold mb-4">Pending Award Requests</h2>

@if($pendingRequests->isEmpty())
<div class="text-gray-500">No pending award requests.</div>
@else
<table class="w-full border text-sm">
<thead class="bg-gray-200 text-center">
<tr>
    <th class="border p-2">Event</th>
    <th class="border p-2">Submitted By</th>
    <th class="border p-2">Division</th>
    <th class="border p-2">Action</th>
</tr>
</thead>
<tbody>
@foreach($pendingRequests as $req)
<tr class="text-center">
    <td class="border p-2">{{ $req->event->name }}</td>
    <td class="border p-2">{{ $req->submitter->name }}</td>
    <td class="border p-2">{{ $req->division }}</td>
    <td class="border p-2">
        <form method="POST" action="{{ route('award_requests.validate', $req->id) }}">
        @csrf
        <button class="bg-green-600 text-white px-4 py-1 rounded">
            Validate & Save
        </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
@endif
@endif

</div>

<script>
function awardApp(){
    return {
        activeTab: 'Elementary Boys',
        currentDivision: 'Elementary',
        currentCategory: 'Boys',
        results: {},
        setTab(name, division, category){
            this.activeTab = name
            this.currentDivision = division
            this.currentCategory = category
        }
    }
}
</script>

@endsection
