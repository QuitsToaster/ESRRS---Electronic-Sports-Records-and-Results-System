@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\Athlete;

$user = auth()->user();

$currentEvent = \App\Models\Event::where('is_active', 1)->first();

// ====================== FILTER TABS ======================
$allTabs = [
    'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
    'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
    'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
    'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
    'Para Games'        => ['division' => 'Paragames', 'category' => null],
];

if ($user->role->name !== 'Super Admin') {
    $tabs = array_filter($allTabs, function($tab) use ($user) {
        $levelOk = in_array($tab['division'], $user->level_access ?? []);
        $categoryOk = is_null($tab['category']) || in_array($tab['category'], $user->category_access ?? []);
        return $levelOk && $categoryOk;
    });
} else {
    $tabs = $allTabs;
}

// ====================== FILTER DIVISIONS ======================
$allDivisions = \App\Models\Division::orderBy('name')->get();

if ($user->role->name !== 'Super Admin') {
    $divisions = $allDivisions->filter(fn($d) => $d->name === $user->division_name);
} else {
    $divisions = $allDivisions;
}


// ================= PREFILL INDIVIDUAL AWARDS (APPROVED ONLY) =================
$prefillIndividual = [];

foreach ($tabs as $tabName => $tab) {

    $query = IndividualAwardResult::where('division', $tab['division'])
        ->where('status', 'approved');

    if (!is_null($tab['category'])) {
        $query->where('category', $tab['category']);
    } else {
        $query->whereNull('category');
    }

    if ($user->role->name !== 'Super Admin') {
        $query->whereIn('sport_id', $user->sports_access ?? [])
              ->where('sdo', $user->division_name);
    }

    foreach ($query->get() as $r) {
        $prefillIndividual[$tabName][$r->sport_id][$r->type_id ?? 0][$r->sdo][$r->medal] = $r->athlete_id;
    }
}

// ================= PENDING AWARDS (NO EVENT FILTER) =================
$pendingResults = collect();
$pendingIndividual = collect();

if ($user->role->name === 'Tournament Manager' || $user->role->name === 'Super Admin') {

    $pendingResultsQuery = AllAwardResult::where('status','pending');
    $pendingIndividualQuery = IndividualAwardResult::where('status','pending');

    if ($user->role->name !== 'Super Admin') {

        $pendingResultsQuery->where('sdo', $user->division_name)
                            ->whereIn('sport_id', $user->sports_access ?? []);

        $pendingIndividualQuery->where('sdo', $user->division_name)
                               ->whereIn('sport_id', $user->sports_access ?? []);
    }

    $pendingResults = $pendingResultsQuery->get();
    $pendingIndividual = $pendingIndividualQuery->get();
}

@endphp

<div x-data="awardApp()" class="p-4 space-y-6">

<!-- {{-- ================= SUCCESS MODAL FOR FORWARD ================= --}}
@if(session('success'))
<div x-data="{ open: true }"
     x-show="open"
     class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center"
     x-cloak>
    <div class="bg-white w-full max-w-md rounded-lg p-6 text-center shadow-xl">
        <h3 class="text-lg font-bold text-green-700 mb-2">
            Success
        </h3>
        <p class="text-gray-800 mb-4">
            {{ session('success') }}
        </p>
        <button @click="open = false"
                class="px-4 py-2 bg-green-600 text-white rounded">
            OK
        </button>
    </div>
</div>
@endif -->

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>Select the award type: <strong>All Awards</strong> for medal tallies per sport or <strong>Individual Awards</strong> for specific athletes.</li>
        <li>Use the tabs above to switch between divisions and categories (e.g., Elementary Boys, Secondary Girls, Para Games).</li>
        <li>For <strong>All Awards</strong>, enter the number of Gold, Silver, and Bronze medals for each SDO in the table.</li>
        <li>For <strong>Individual Awards</strong>, select the athletes who won Gold, Silver, and Bronze for each sport and type.</li>
        <li>After filling the data, click the <strong>Save</strong> button at the bottom of each section to store your entries.</li>
    </ul>
</div>

@if(!$currentEvent)
<div class="p-4 mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 rounded shadow-sm">
    <strong>No Active Event Found.</strong>
    You can still open this page, but results cannot be submitted until an event is activated.
</div>
@endif

{{-- ============================================================= --}}
{{-- 🟡 PENDING AWARDS NEEDING REVISION (TOURNAMENT SECRETARY) --}}
{{-- ============================================================= --}}
@if($user->role->name === 'Tournament Secretary')

@php
$pendingResultsSecretary = \App\Models\AllAwardResult::with(['sport','type'])
    ->where('status','needs_revision')
    ->where('sdo','like','%' . $user->division_name . '%')

    // ✅ FIX: allow NULL type_id
    ->where(function($q) use ($user) {
        $q->whereNull('type_id')
          ->orWhereIn('type_id', $user->sport_type_access ?? []);
    })

    ->whereIn('sport_id', $user->sports_access ?? [])
    ->whereIn('division', $user->level_access ?? [])

    ->where(function($q) use ($user) {
        $q->whereNull('category')
          ->orWhereIn('category', $user->category_access ?? []);
    })

    ->latest()
    ->get();

$pendingIndividualSecretary = \App\Models\IndividualAwardResult::with(['sport','type','athlete'])
    ->where('status','needs_revision')
    ->where('sdo','like','%' . $user->division_name . '%')

    ->whereIn('sport_id', $user->sports_access ?? [])
    ->whereIn('type_id', $user->sport_type_access ?? [])
    ->whereIn('division', $user->level_access ?? [])

    ->where(function($q) use ($user) {
        $q->whereNull('category')
          ->orWhereIn('category', $user->category_access ?? []);
    })

    ->latest()
    ->get();
@endphp

<div class="p-4 mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 rounded shadow-sm">

<h3 class="font-semibold text-lg mb-3">
Awards Returned for Revision
</h3>

{{-- ================= ALL AWARDS REVISION ================= --}}
@if($pendingResultsSecretary->count())
<h4 class="font-semibold mb-2">All Awards</h4>

<table class="w-full border text-sm mb-6">
<thead class="bg-red-200 text-center">
<tr>
<th class="border px-2">Division</th>
<th class="border px-2">Category</th>
<th class="border px-2">Sport</th>
<th class="border px-2">Event Type</th>
<th class="border px-2">Medal</th>
<th class="border px-2">SDO</th>
<th class="border px-2">Revision Message</th>
<th class="border px-2">Validation</th>
<th class="border px-2">Action</th>
</tr>
</thead>

<tbody>
@foreach($pendingResultsSecretary as $r)
<tr class="text-center">
<td class="border px-2">{{ $r->division }}</td>
<td class="border px-2">{{ $r->category ?? '—' }}</td>
<td class="border px-2">{{ $r->sport->name ?? 'N/A' }}</td>

{{-- ✅ SAFE TYPE DISPLAY --}}
<td class="border px-2">
    {{ $r->type->type_name ?? '-' }}
</td>

<td class="border px-2 font-semibold">{{ $r->medal }}</td>
<td class="border px-2">{{ $r->sdo }}</td>
<td class="border px-2 text-left">{{ $r->revision_message ?? 'No message' }}</td>

<td class="border px-2">
@if($r->validation_file)
@php
$fileUrl = asset('storage/'.$r->validation_file);
$ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
@endphp

@if(in_array($ext,['jpg','jpeg','png','gif','webp']))
<a href="{{ $fileUrl }}" target="_blank">
<img src="{{ $fileUrl }}" class="h-12 mx-auto rounded shadow">
</a>
@else
<a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 underline">
View File
</a>
@endif
@else
<span class="text-gray-400 italic">No File</span>
@endif
</td>

<td class="border px-2">
<form method="POST" action="{{ route('all-awards.revise', $r->id) }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500">
        Resubmit
    </button>
</form>
</td>

</tr>
@endforeach
</tbody>
</table>
@endif

{{-- ================= INDIVIDUAL AWARDS REVISION ================= --}}
@if($pendingIndividualSecretary->count())
<h4 class="font-semibold mb-2">Individual Awards</h4>

<table class="w-full border text-sm">
<thead class="bg-red-200 text-center">
<tr>
<th class="border px-2">Division</th>
<th class="border px-2">Category</th>
<th class="border px-2">Sport</th>
<th class="border px-2">Event Type</th>
<th class="border px-2">Medal</th>
<th class="border px-2">Athlete</th>
<th class="border px-2">SDO</th>
<th class="border px-2">Revision Message</th>
<th class="border px-2">Validation</th>
<th class="border px-2">Action</th>
</tr>
</thead>

<tbody>
@foreach($pendingIndividualSecretary as $r)
<tr class="text-center">
<td class="border px-2">{{ $r->division }}</td>
<td class="border px-2">{{ $r->category ?? '—' }}</td>
<td class="border px-2">{{ $r->sport->name ?? 'N/A' }}</td>
<td class="border px-2">{{ $r->type->type_name ?? '-' }}</td>
<td class="border px-2 font-semibold">{{ $r->medal }}</td>
<td class="border px-2">{{ $r->athlete->last_name ?? '' }}, {{ $r->athlete->first_name ?? '' }}</td>
<td class="border px-2">{{ $r->sdo }}</td>
<td class="border px-2 text-left">{{ $r->revision_message ?? 'No message' }}</td>

<td class="border px-2">
@if($r->validation_file)
@php
$fileUrl = asset('storage/'.$r->validation_file);
$ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
@endphp

@if(in_array($ext,['jpg','jpeg','png','gif','webp']))
<a href="{{ $fileUrl }}" target="_blank">
<img src="{{ $fileUrl }}" class="h-12 mx-auto rounded shadow">
</a>
@else
<a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 underline">
View File
</a>
@endif
@else
<span class="text-gray-400 italic">No File</span>
@endif
</td>

<td class="border px-2">
<form method="POST" action="{{ route('all-awards.revise', $r->id) }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500">
        Resubmit
    </button>
</form>
</td>

</tr>
@endforeach
</tbody>
</table>
@endif

@if($pendingResultsSecretary->count() === 0 && $pendingIndividualSecretary->count() === 0)
<p class="text-gray-600">
No awards returned for revision for your assigned sports.
</p>
@endif

</div>
@endif

{{-- ================= PENDING AWARDS FOR TOURNAMENT MANAGER / SUPER ADMIN ================= --}}
@php

$pendingResults = collect();
$pendingIndividual = collect();

if ($user->role->name === 'Tournament Manager' || $user->role->name === 'Super Admin') {

    $pendingResultsQuery = \App\Models\AllAwardResult::with(['sport','type'])
        ->where('status','pending');

    $pendingIndividualQuery = \App\Models\IndividualAwardResult::with(['sport','type','athlete'])
        ->where('status','pending');

    if ($user->role->name !== 'Super Admin') {

        // ✅ FIX HERE (ALLOW NULL type_id)
        $pendingResultsQuery->whereIn('sport_id', $user->sports_access ?? [])
                            ->where(function($q) use ($user) {
                                $q->whereNull('type_id')
                                  ->orWhereIn('type_id', $user->sport_type_access ?? []);
                            })
                            ->whereIn('division', $user->level_access ?? [])
                            ->where(function($q) use ($user) {
                                $q->whereNull('category')
                                  ->orWhereIn('category', $user->category_access ?? []);
                            });

        $pendingIndividualQuery->whereIn('sport_id', $user->sports_access ?? [])
                               ->whereIn('type_id', $user->sport_type_access ?? [])
                               ->whereIn('division', $user->level_access ?? [])
                               ->where(function($q) use ($user) {
                                   $q->whereNull('category')
                                     ->orWhereIn('category', $user->category_access ?? []);
                               });
    }

    $pendingResults = $pendingResultsQuery->latest()->get();
    $pendingIndividual = $pendingIndividualQuery->latest()->get();
}

@endphp

{{-- ================= SUCCESS MODAL ================= --}}
@if(session('success'))
<div x-data="{ open: true }"
     x-show="open"
     class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center">

    <div class="bg-white w-full max-w-md rounded-lg p-6 text-center shadow-xl">
        <h3 class="text-lg font-bold text-green-700 mb-2">
            Success
        </h3>

        <p class="text-gray-800 mb-4">
            {{ session('success') }}
        </p>

        <button @click="open = false"
                class="px-4 py-2 bg-green-600 text-white rounded">
            OK
        </button>
    </div>
</div>
@endif

@php
    $userRole = optional(auth()->user()->role)->name;
@endphp

@if(in_array($userRole, ['Super Admin', 'Tournament Manager']))
<div class="p-4 mb-6 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded shadow-sm">

    <h3 class="font-semibold mb-2">Pending Awards Needing Approval</h3>

    {{-- ================= ALL AWARDS ================= --}}
    @if($pendingResults->count())
        <h4 class="font-semibold mt-2 mb-1">All Awards</h4>

        <table class="w-full border text-sm mb-4">
            <thead class="bg-yellow-200 text-center">
                <tr>
                    <th class="border px-2">Sport</th>
                    <th class="border px-2">Event Type</th>
                    <th class="border px-2">SDO</th>
                    <th class="border px-2">Medal</th>
                    <th class="border px-2">Count</th>
                    <th class="border px-2">Validation File</th>
                    <th class="border px-2">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($pendingResults as $r)
                    <tr class="text-center">
                        <td class="border px-2">{{ $r->sport->name ?? 'N/A' }}</td>

                        {{-- ✅ SAFE TYPE DISPLAY --}}
                        <td class="border px-2">
                            {{ $r->type->type_name ?? 'ALL EVENTS' }}
                        </td>

                        <td class="border px-2">{{ $r->sdo }}</td>
                        <td class="border px-2">{{ $r->medal }}</td>
                        <td class="border px-2">{{ $r->count }}</td>

                        {{-- VALIDATION FILE --}}
                        <td class="border px-2">
                            @if($r->validation_file)
                                @php
                                    $fileUrl = asset('storage/' . $r->validation_file);
                                    $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                                @endphp

                                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                    <a href="{{ $fileUrl }}" target="_blank">
                                        <img src="{{ $fileUrl }}"
                                             class="h-12 mx-auto rounded shadow hover:scale-105 transition">
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank"
                                       class="text-blue-600 underline font-semibold">
                                        View PDF
                                    </a>
                                @endif
                            @else
                                <span class="text-gray-400 italic">No file</span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="border px-2 space-x-1">
                            {{-- APPROVE --}}
                            <form method="POST"
                                  action="{{ route('approve_all_awards') }}"
                                  class="inline">
                                @csrf
                                <input type="hidden" name="award_id" value="{{ $r->id }}">
                                <button class="bg-green-600 text-white px-3 py-1 rounded">
                                    Approve
                                </button>
                            </form>

                            {{-- REJECT --}}
                            <button
                                @click="$dispatch('open-reject', { id: {{ $r->id }}, type: 'all' })"
                                class="bg-red-600 text-white px-3 py-1 rounded">
                                Reject
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ================= REJECT MODAL ================= --}}
    <div x-data="{ open:false, awardId:null, awardType:null, message:'' }"
         x-on:open-reject.window="
            open=true;
            awardId=$event.detail.id;
            awardType=$event.detail.type;
         ">

        <div x-show="open"
             class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center">

            <div class="bg-white w-full max-w-md rounded-lg p-6">
                <h3 class="font-bold text-lg mb-3">Reject Award</h3>

                <textarea x-model="message"
                          class="w-full border rounded p-2 mb-4"
                          placeholder="Enter revision message..."
                          required></textarea>

                <form method="POST"
                      :action="awardType === 'all'
                        ? '{{ route('reject_all_awards') }}'
                        : '{{ route('reject_individual_awards') }}'">

                    @csrf
                    <input type="hidden" name="award_id" :value="awardId">
                    <input type="hidden" name="message" :value="message">

                    <div class="flex justify-end gap-2">
                        <button type="button"
                                @click="open=false"
                                class="px-4 py-2 bg-gray-400 rounded">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded">
                            Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= INDIVIDUAL AWARDS ================= --}}
    @if($pendingIndividual->count())
        <h4 class="font-semibold mt-2 mb-1">Individual Awards</h4>

        <table class="w-full border text-sm">
            <thead class="bg-yellow-200 text-center">
                <tr>
                    <th class="border px-2">Sport</th>
                    <th class="border px-2">Event Type</th>
                    <th class="border px-2">SDO</th>
                    <th class="border px-2">Medal</th>
                    <th class="border px-2">Athlete</th>
                    <th class="border px-2">Validation File</th>
                    <th class="border px-2">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($pendingIndividual as $r)
                    <tr class="text-center">
                        <td class="border px-2">{{ $r->sport->name ?? 'N/A' }}</td>
                        <td class="border px-2">{{ $r->type->type_name ?? '-' }}</td>
                        <td class="border px-2">{{ $r->sdo }}</td>
                        <td class="border px-2">{{ $r->medal }}</td>
                        <td class="border px-2">
                            {{ $r->athlete ? $r->athlete->last_name . ', ' . $r->athlete->first_name : '-' }}
                        </td>

                        {{-- VALIDATION FILE --}}
                        <td class="border px-2">
                            @if($r->validation_file)
                                @php
                                    $fileUrl = asset('storage/' . $r->validation_file);
                                    $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                                @endphp

                                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                    <a href="{{ $fileUrl }}" target="_blank">
                                        <img src="{{ $fileUrl }}"
                                             class="h-12 mx-auto rounded shadow cursor-pointer hover:scale-105 transition"
                                             alt="Validation Image">
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="text-blue-600 underline font-semibold">
                                        View PDF
                                    </a>
                                @endif
                            @else
                                <span class="text-gray-400 italic">No file</span>
                            @endif
                        </td>

                        <td class="border px-2 space-x-1">
                            {{-- APPROVE --}}
                            <button
                                class="bg-green-500 text-white px-3 py-1 rounded"
                                @click="$dispatch('open-modal', { awardId: {{ $r->id }}, type: 'individual' })">
                                Approve
                            </button>

                            {{-- REJECT --}}
                            <button
                                @click="$dispatch('open-reject', { id: {{ $r->id }}, type: 'individual' })"
                                class="bg-red-600 text-white px-3 py-1 rounded">
                                Reject
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($pendingResults->count() === 0 && $pendingIndividual->count() === 0)
        <p>No pending awards to approve for your division/event.</p>
    @endif
</div>
@endif

{{-- ================= APPROVE CONFIRMATION MODAL ================= --}}
<div
    x-data="{ showModal: false, awardId: null, awardType: null }"
    x-on:open-modal.window="showModal = true; awardId = $event.detail.awardId; awardType = $event.detail.type;"
    x-cloak
>
    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded shadow p-6 w-96">
            <h3 class="text-lg font-bold mb-4">Confirm Approval</h3>
            <p class="mb-6">Are you sure you want to approve this award? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 border rounded" @click="showModal=false">Cancel</button>

                <form :action="awardType === 'all' ? '{{ route('approve_all_awards') }}' : '{{ route('approve_individual_awards') }}'" method="POST">
                    @csrf
                    <input type="hidden" name="award_id" :value="awardId">
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Yes, Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>



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
// ==============================
// EVENTS QUERY (REPLACES SPORTS)
// ==============================
$eventsQuery = \App\Models\Event::query()
    ->where('age_group', $tab['division'])
    ->where('is_active', 1);

// FILTER CATEGORY
if (!is_null($tab['category'])) {
    $eventsQuery->where('category', $tab['category']);
}

// 🔒 NON-SUPER ADMIN FILTER
if ($user->role->name !== 'Super Admin') {
    $eventsQuery->whereIn('sport_id', $user->sports_access ?? []);
}

// PAGINATE EVENTS
$events = $eventsQuery->orderBy('name')->paginate(10);

// -----------------------------
// FILTER ATHLETES FOR THIS TAB
// -----------------------------
$tabAthletes = $athletes->filter(fn($a) =>
    $a->division == $tab['division'] &&
    ($tab['category'] ? $a->category == $tab['category'] : true) &&
    ($user->role->name === 'Super Admin' || $a->sdo == $user->division_name)
)->sortBy('last_name');
@endphp
@php
    // Accessible sports for this tab
    if($user->role->name === 'Tournament Manager') {
        $accessibleSportIds = is_string($user->sports_access) ? json_decode($user->sports_access, true) : $user->sports_access;
        $accessibleSports = \App\Models\Sport::whereIn('id', $accessibleSportIds)->get();
        $accessibleTypeIds = is_string($user->sport_type_access) ? json_decode($user->sport_type_access, true) : $user->sport_type_access;
    } else {
        $accessibleSports = $sports; // default to all sports fetched above
        $accessibleTypeIds = null;
    }
@endphp

        <div x-show="activeTab==='{{ $tabName }}'" x-cloak>

            <h2 class="text-xl font-bold mb-4">{{ $tabName }}</h2>

          

            
@php
$divisions = \App\Models\Division::orderBy('name')->get();
@endphp


{{-- ================================================= --}}
{{-- 🏆 ALL AWARDS FORM / VIEW --}}
{{-- ================================================= --}}
<div x-show="awardType==='all'">

    {{-- ================= NORMAL FORM ================= --}}
    @if(in_array($user->role->name, ['Super Admin','Tournament Secretary']))

        <form method="POST"
              action="{{ route('results_all_awards.store') }}"
              enctype="multipart/form-data"
              x-data="{ showModal:false }">

            @csrf

            <input type="hidden" name="division" value="{{ $tab['division'] }}">
            <input type="hidden" name="category" value="{{ $tab['category'] ?? '' }}">

            @foreach($events as $event)
                @php
                    if (!is_object($event)) continue;
                @endphp

                <h3 class="font-semibold mt-6">
                    {{ strtoupper($event->name ?? 'Unnamed Event') }}
                </h3>

                <table class="w-full border text-sm mt-2">
                    <thead class="bg-gray-200 text-center">
                        <tr>
                            <th class="border px-2 py-2">Event</th>
                            <th class="border bg-yellow-300">Gold</th>
                            <th class="border bg-gray-300">Silver</th>
                            <th class="border bg-orange-200">Bronze</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="border px-2 font-medium">{{ $event->name ?? 'N/A' }}</td>

                            @foreach(['Gold','Silver','Bronze'] as $medal)
                                <td class="border px-2 py-2 align-top">
                                    <div x-data="{ divisions:[''] }">

                                        <template x-for="(division,index) in divisions" :key="index">
                                            <div class="flex gap-2 mb-2">

                                                <select
                                                    :name="'results[{{ $event->id }}][{{ $event->sport_id ?? 0 }}][{{ $medal }}][]'"
                                                    class="w-full border rounded px-2 py-1 text-sm"
                                                >
                                                    <option value="">-- Select Division / SDO --</option>
                                                    @foreach($divisions as $divisionOption)
                                                        <option value="{{ $divisionOption->name }}">
                                                            {{ $divisionOption->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="button"
                                                        x-show="divisions.length > 1"
                                                        @click="divisions.splice(index,1)"
                                                        class="bg-red-500 text-white px-2 rounded text-xs">
                                                    ✕
                                                </button>

                                            </div>
                                        </template>

                                        <button type="button"
                                                @click="divisions.push('')"
                                                class="text-blue-600 text-xs hover:underline">
                                            + Add Division
                                        </button>

                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            @endforeach

            <div class="flex justify-end mt-4">
                @if($user->role->name === 'Super Admin')
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">
                        Save All Awards
                    </button>
                @else
                    <button type="button"
                            @click="showModal = true"
                            class="bg-yellow-500 text-white px-6 py-2 rounded">
                        Forward for Validation
                    </button>
                @endif
            </div>

            {{-- OPTIONAL MODAL --}}
            <div x-show="showModal" x-cloak
                 class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded shadow w-96">

                    <h3 class="text-lg font-bold mb-4">Confirm Forward</h3>

                    <input type="file" name="validation_file" class="w-full border rounded mb-4">

                    <div class="flex justify-end gap-2">
                        <button type="button"
                                @click="showModal=false"
                                class="px-4 py-2 bg-gray-400 text-white rounded">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-yellow-500 text-white rounded">
                            Yes, Forward
                        </button>
                    </div>
                </div>
            </div>

        </form>

    @endif

</div>


{{-- ========================================================= --}}
{{-- 🏅 INDIVIDUAL AWARDS FORM --}}
{{-- ========================================================= --}}
<div x-show="awardType==='individual'" x-data="individualAwardsForm()">

    @php
        $userRole = optional($user->role)->name;
    @endphp

    <form method="POST" 
          action="{{ $userRole === 'Tournament Secretary' ? route('results_individual.store') : route('results_individual.store') }}" 
          enctype="multipart/form-data"
          x-data="{ showModal: false }">

        @csrf

        <input type="hidden" name="event_id" value="{{ $currentEvent?->id }}">
        <input type="hidden" name="division" value="{{ $tab['division'] }}">
        @if(!is_null($tab['category']))
            <input type="hidden" name="category" value="{{ $tab['category'] }}">
        @endif

        @php
            $athletes = \App\Models\Athlete::with('school')->orderBy('last_name')->get();
        @endphp

        @foreach($events as $event)
            @php if (!is_object($event)) continue; @endphp

            <h3 class="font-semibold mt-6">
                {{ strtoupper($event->name ?? 'Unnamed Event') }}
            </h3>

            <table class="w-full border text-sm mt-2">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-2">Event</th>
                        <th class="border px-2 py-2 bg-yellow-100">Gold</th>
                        <th class="border px-2 py-2 bg-gray-200">Silver</th>
                        <th class="border px-2 py-2 bg-orange-200">Bronze</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="border px-2 font-medium">{{ $event->name ?? 'N/A' }}</td>

                        @foreach(['Gold','Silver','Bronze'] as $medal)
                            <td class="border px-3 py-3 align-top">

                                {{-- Searchable Athlete Input --}}
                                <div x-data="{ selectedAthlete: null }" class="space-y-1">
                                    <input 
                                        list="athletes_list_{{ $event->sport_id }}_{{ $event->sport_type_id }}_{{ $medal }}" 
                                        x-model="selectedAthlete" 
                                        placeholder="Type athlete name..." 
                                        class="w-full border rounded px-2 py-1 text-sm">

                                    <datalist id="athletes_list_{{ $event->sport_id }}_{{ $event->sport_type_id }}_{{ $medal }}">
                                        @foreach($athletes as $athlete)
                                            <option value="{{ $athlete->last_name }}, {{ $athlete->first_name }}"
                                                    data-sdo="{{ $athlete->school_division ?? '' }}"
                                                    data-school="{{ $athlete->school?->name ?? '' }}">
                                            </option>
                                        @endforeach
                                    </datalist>

                                    {{-- Hidden inputs to submit athlete ID and auto-fill SDO --}}
                                    <input type="hidden" 
                                           :value="getAthleteId(selectedAthlete)" 
                                           name="individual_results[{{ $event->sport_id }}][{{ $event->sport_type_id }}][{{ $medal }}][athlete]">
                                    <input type="hidden"
                                           :value="getAthleteSDO(selectedAthlete)"
                                           name="individual_results[{{ $event->sport_id }}][{{ $event->sport_type_id }}][{{ $medal }}][sdo]">

                                    {{-- Display school division & school name --}}
                                    <div class="text-xs text-gray-600 mt-1" x-html="getAthleteInfo(selectedAthlete)"></div>
                                </div>

                            </td>
                        @endforeach

                    </tr>
                </tbody>
            </table>
        @endforeach

        <div class="flex justify-end mt-6">
            @if($userRole === 'Super Admin')
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">
                    Save Individual Awards
                </button>
            @elseif($userRole === 'Tournament Secretary')
                <button type="button"
                        @click="showModal=true"
                        class="bg-yellow-500 text-white px-6 py-2 rounded">
                    Forward for Validation
                </button>
            @endif
        </div>

        {{-- Forward for Validation Modal --}}
        <div x-show="showModal" x-cloak
             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow w-96">

                <h3 class="text-lg font-bold mb-4">Confirm Forward</h3>

                <input type="file" name="validation_file" class="w-full border rounded mb-4">

                <div class="flex justify-end gap-2">
                    <button type="button"
                            @click="showModal=false"
                            class="px-4 py-2 bg-gray-400 text-white rounded">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-4 py-2 bg-yellow-500 text-white rounded">
                        Yes, Forward
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
function individualAwardsForm() {
    return {
        athletesData: [
            @foreach($athletes as $athlete)
                {
                    id: "{{ $athlete->id }}",
                    name: "{{ $athlete->last_name }}, {{ $athlete->first_name }}",
                    sdo: "SDO - {{ $athlete->school_division ?? '' }}",
                    school: "{{ $athlete->school?->name ?? '' }}"
                },
            @endforeach
        ],

        getAthleteId(selectedName) {
            let athlete = this.athletesData.find(a => a.name === selectedName);
            return athlete ? athlete.id : '';
        },

        getAthleteSDO(selectedName) {
            let athlete = this.athletesData.find(a => a.name === selectedName);
            return athlete ? athlete.sdo : '';
        },

        getAthleteInfo(selectedName) {
            let athlete = this.athletesData.find(a => a.name === selectedName);
            return athlete ? `${athlete.sdo}<br>${athlete.school}` : '';
        }
    }
}
</script>
    @endforeach

</div>

<script>
function awardApp(){
    return {
        activeTab:'{{ array_key_first($tabs) }}',
        awardType:'all',
        individualResults:@json($prefillIndividual),
    }
}
</script>

@endsection