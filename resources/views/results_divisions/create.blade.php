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
        ->where('status', 'pending')
        ->with('submitter', 'event')
        ->latest()
        ->get();
}
@endphp

{{-- ================= FLASH MESSAGES ================= --}}
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

<div x-data="awardApp()" class="space-y-6">

{{-- ================= MEDAL TALLY (TOP TABLE RESTORED) ================= --}}
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
$g = \App\Models\AllAwardResult::where('sdo',$division->name)->where('medal','Gold')->sum('count');
$s = \App\Models\AllAwardResult::where('sdo',$division->name)->where('medal','Silver')->sum('count');
$b = \App\Models\AllAwardResult::where('sdo',$division->name)->where('medal','Bronze')->sum('count');
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


{{-- SECRETARY / SUPER ADMIN FORM --}}
@if($isSecretary || $isSuperAdmin)

<form x-ref="awardForm"
      method="POST"
      @if($isSecretary)
      action="{{ route('results_secretary.storeSecretaryAwards') }}" {{-- Fixed route --}}
      @else
      action="{{ route('results_all_awards.storeAllAwards') }}"
      @endif
      @if(!$activeEvent) onsubmit="return false;" @endif>

@csrf

<input type="hidden" name="event_id" value="{{ $activeEvent?->id }}">
<input type="hidden" name="award_type" value="all">
<input type="hidden" name="division" x-model="currentDivision" x-ref="divisionInput">
<input type="hidden" name="category" x-model="currentCategory" x-ref="categoryInput">
<textarea name="results" x-text="JSON.stringify(results)" hidden></textarea>

@if($isSuperAdmin)
<input type="hidden" name="results" :value="JSON.stringify(results)">
@endif

{{-- ================= TABS ================= --}}
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

{{-- ================= PER TAB ================= --}}
@foreach($tabs as $tabName => $tab)
@php
$sports = Sport::with('types')->where('division', $tab['division'])->get();
@endphp

<div x-show="activeTab === '{{ $tabName }}'">

@foreach($sports as $sport)
<h3 class="font-semibold mt-6 mb-2">{{ strtoupper($sport->name) }}</h3>

<table class="w-full border text-sm">
<thead class="bg-gray-100 text-center">
<tr>
    <th rowspan="2" class="border p-2">Event</th>
    @foreach($divisions as $d)
        <th colspan="3" class="border p-2 font-semibold">{{ $d->name }}</th>
    @endforeach
</tr>
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

{{-- ================= ACTION BUTTON ================= --}}
<div class="flex justify-end mt-6">
@if($isSecretary)
<button type="button"
    @click="submitSecretaryForm()"
    class="px-6 py-2 rounded text-white bg-blue-600">
    Forward for Validation
</button>
@else
<button type="submit"
    class="px-6 py-2 rounded text-white bg-green-600">
    Save Awards
</button>
@endif
</div>

{{-- ================= CONFIRMATION MODAL ================= --}}
<div x-show="confirmOpen" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
<div class="bg-white rounded-lg w-full max-w-md p-6">
<h2 class="text-lg font-bold mb-3">Confirm Submission</h2>
<p class="text-gray-600 mb-6">
Forward this award request to the Tournament Manager?
</p>
<div class="flex justify-end space-x-3">
<button type="button" @click="confirmOpen=false" class="px-4 py-2 border rounded">
Cancel
</button>
<button type="button"
    @click="$refs.awardForm.submit()"
    class="px-4 py-2 bg-blue-600 text-white rounded">
Yes, Forward
</button>
</div>
</div>
</div>

</form>
@endif

<script>
function awardApp(){
    return {
        activeTab: 'Elementary Boys',
        currentDivision: 'Elementary',
        currentCategory: 'Boys',
        results: {},
        confirmOpen: false,
        setTab(name, division, category){
            this.activeTab = name
            this.currentDivision = division
            this.currentCategory = category
        },
        submitSecretaryForm(){
            // Update hidden inputs before submitting
            this.$refs.divisionInput.value = this.currentDivision;
            this.$refs.categoryInput.value = this.currentCategory;
            this.$refs.awardForm.submit();
        }
    }
}
</script>

@endsection
