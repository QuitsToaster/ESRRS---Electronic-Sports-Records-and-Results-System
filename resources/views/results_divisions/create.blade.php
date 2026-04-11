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

$sdoList = \App\Models\Division::orderBy('name')->pluck('name');


$pendingResultsSecretary = \App\Models\AllAwardResult::with(['sport','type'])
    ->where('status','needs_revision')
    ->where('sdo','like','%' . $user->division_name . '%')

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

<div x-data="editAwardModal()" class="p-6 mb-6 bg-white rounded-2xl shadow-sm border border-gray-100">

    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-red-50 rounded-xl">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Awards Returned for Revision</h3>
    </div>

    {{-- ================= ALL AWARDS ================= --}}
    @if($pendingResultsSecretary->count())
        <div class="mb-8">
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                All Awards
            </h4>

            <div class="grid gap-4">
                @foreach($pendingResultsSecretary as $r)
                    <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-3">
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">{{ $r->division }}</span>
                                    @if($r->category)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ $r->category }}</span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-3 flex-wrap mb-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $r->sport->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-sm text-gray-600">{{ $r->type->type_name ?? '-' }}</span>
                                </div>
                                
                                <div class="flex flex-wrap gap-4 text-sm mb-3">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                            @if($r->medal === 'Gold') bg-amber-100 text-amber-700
                                            @elseif($r->medal === 'Silver') bg-gray-100 text-gray-700
                                            @elseif($r->medal === 'Bronze') bg-orange-100 text-orange-700
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $r->medal }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-gray-600">{{ $r->sdo }}</span>
                                    </div>
                                </div>
                                
                                <div class="bg-red-50 border-l-3 border-red-400 p-3 rounded-lg mt-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <p class="text-sm text-red-700">{{ $r->revision_message ?? 'No message' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- VALIDATION FILE --}}
                                @if($r->validation_file)
                                    @php
                                        $fileUrl = asset('storage/'.$r->validation_file);
                                        $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(in_array($ext,['jpg','jpeg','png','gif','webp']))
                                        <a href="{{ $fileUrl }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition flex-shrink-0">
                                            <img src="{{ $fileUrl }}" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            View File
                                        </a>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm flex-shrink-0">—</span>
                                @endif

                                {{-- EDIT BUTTON --}}
                                <button @click="openModal({{ $r->id }}, 'all', '{{ $r->medal }}', '{{ $r->sdo }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm flex-shrink-0">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ================= INDIVIDUAL AWARDS ================= --}}
    @if($pendingIndividualSecretary->count())
        <div>
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                Individual Awards
            </h4>

            <div class="grid gap-4">
                @foreach($pendingIndividualSecretary as $r)
                    <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-3">
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">{{ $r->division }}</span>
                                    @if($r->category)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ $r->category }}</span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-3 flex-wrap mb-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $r->sport->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-sm text-gray-600">{{ $r->type->type_name ?? '-' }}</span>
                                </div>
                                
                                <div class="flex flex-wrap gap-4 text-sm mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                            @if($r->medal === 'Gold') bg-amber-100 text-amber-700
                                            @elseif($r->medal === 'Silver') bg-gray-100 text-gray-700
                                            @elseif($r->medal === 'Bronze') bg-orange-100 text-orange-700
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $r->medal }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $r->athlete->last_name ?? '' }}, {{ $r->athlete->first_name ?? '' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-gray-600">{{ $r->sdo }}</span>
                                    </div>
                                </div>
                                
                                <div class="bg-red-50 border-l-3 border-red-400 p-3 rounded-lg mt-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <p class="text-sm text-red-700">{{ $r->revision_message ?? 'No message' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- VALIDATION FILE --}}
                                @if($r->validation_file)
                                    @php
                                        $fileUrl = asset('storage/'.$r->validation_file);
                                        $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(in_array($ext,['jpg','jpeg','png','gif','webp']))
                                        <a href="{{ $fileUrl }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition flex-shrink-0">
                                            <img src="{{ $fileUrl }}" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            View File
                                        </a>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm flex-shrink-0">—</span>
                                @endif

                                {{-- EDIT BUTTON --}}
                                <button @click="openModal({{ $r->id }}, 'individual', '{{ $r->medal }}', '{{ $r->sdo }}')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm flex-shrink-0">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($pendingResultsSecretary->count() === 0 && $pendingIndividualSecretary->count() === 0)
        <div class="text-center py-10">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-gray-500">No awards returned for revision.</p>
        </div>
    @endif

    {{-- ================= EDIT MODAL ================= --}}
    <div x-show="show" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" x-cloak>
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 rounded-full">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Edit & Resubmit Award</h3>
            </div>

            <form method="POST"
                :action="type === 'all'
                    ? '{{ route('update_all_awards') }}'
                    : '{{ route('update_individual_awards') }}'">

                @csrf

                <input type="hidden" name="award_id" :value="awardId">

                {{-- ================= MEDAL ================= --}}
                <label class="block mb-2 text-sm font-medium text-gray-700">Medal</label>
                <select name="medal" x-model="medal" class="w-full border border-gray-200 rounded-xl p-2.5 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option>Gold</option>
                    <option>Silver</option>
                    <option>Bronze</option>
                </select>

                {{-- ================= INDIVIDUAL ONLY ================= --}}
                <template x-if="type === 'individual'">
                    <div>
                        {{-- 🔍 ATHLETE SEARCH --}}
                        <label class="block mb-2 text-sm font-medium text-gray-700">Search Athlete</label>
                        <input type="text"
                            x-model="search"
                            @input="filterAthletes"
                            placeholder="Type athlete name..."
                            class="w-full border border-gray-200 rounded-xl p-2.5 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">

                        {{-- RESULTS --}}
                        <div class="border border-gray-200 rounded-xl max-h-40 overflow-y-auto mb-4 bg-white"
                             x-show="filtered.length" x-cloak>

                            <template x-for="ath in filtered" :key="ath.id">
                                <div @click="selectAthlete(ath)"
                                    class="px-3 py-2 hover:bg-blue-50 cursor-pointer transition">
                                    <span x-text="ath.name" class="text-sm text-gray-800"></span>
                                    <span class="text-xs text-gray-500 ml-2"
                                          x-text="'(' + ath.sdo + ')'"></span>
                                </div>
                            </template>
                        </div>

                        {{-- SELECTED --}}
                        <input type="hidden" name="athlete_id" :value="selectedAthlete?.id">

                        <div class="mb-3 text-sm text-gray-600 bg-gray-50 rounded-lg p-2">
                            Selected: 
                            <span class="font-semibold text-gray-900" x-text="selectedAthlete?.name || 'None'"></span>
                        </div>

                        {{-- AUTO SDO --}}
                        <label class="block mb-2 text-sm font-medium text-gray-700">SDO</label>
                        <input type="text"
                            name="sdo"
                            x-model="sdo"
                            readonly
                            class="w-full border border-gray-200 rounded-xl p-2.5 mb-4 bg-gray-50 text-gray-600 cursor-not-allowed">
                    </div>
                </template>

                {{-- ================= ALL AWARDS ONLY ================= --}}
                <template x-if="type === 'all'">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">SDO</label>
                        <select name="sdo" x-model="sdo" class="w-full border border-gray-200 rounded-xl p-2.5 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                            <option value="">Select SDO</option>
                            @foreach($sdoList as $sdo)
                                <option value="{{ $sdo }}">{{ $sdo }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="show=false"
                        class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition shadow-sm">
                        Resubmit
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@php
$athletes = \App\Models\Athlete::select(
    'id',
    \DB::raw("CONCAT(last_name, ', ', first_name) as name"),
    'school_division as sdo'
)->orderBy('last_name')->get();
@endphp

{{-- ================= ALPINE SCRIPT ================= --}}
<script>
function editAwardModal() {
    return {
        show: false,
        awardId: null,
        type: null,
        medal: '',
        sdo: '',

        // 🔍 athlete search
        search: '',
        athletes: @json($athletes),
        filtered: [],
        selectedAthlete: null,

        openModal(id, type, medal, sdo) {
            this.awardId = id;
            this.type = type;
            this.medal = medal;
            this.sdo = sdo;

            this.search = '';
            this.filtered = [];
            this.selectedAthlete = null;

            this.show = true;
        },

        filterAthletes() {
            let q = this.search.toLowerCase();

            this.filtered = this.athletes.filter(a =>
                a.name.toLowerCase().includes(q)
            ).slice(0, 10);
        },

        selectAthlete(ath) {
            this.selectedAthlete = ath;
            this.search = ath.name;
            this.sdo = ath.sdo; // ✅ AUTO SET SDO
            this.filtered = [];
        }
    }
}
</script>


@endif

{{-- ================= PENDING AWARDS FOR TOURNAMENT MANAGER / SUPER ADMIN ================= --}}
@php
$pendingResults = collect();
$pendingIndividual = collect();

if (in_array($user->role->name, ['Tournament Manager', 'Super Admin'])) {

    // ALL AWARDS PENDING
    $pendingResultsQuery = \App\Models\AllAwardResult::with(['sport','type'])
        ->where('status','pending');

    // INDIVIDUAL AWARDS PENDING
    $pendingIndividualQuery = \App\Models\IndividualAwardResult::with(['sport','type','athlete'])
        ->where('status','pending');

    if ($user->role->name !== 'Super Admin') {

        // FILTER: Sports & Type (allow NULL type_id)
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

        // ✅ INDIVIDUAL AWARDS: SAME LOGIC (allow NULL type_id & category)
        $pendingIndividualQuery->whereIn('sport_id', $user->sports_access ?? [])
                               ->where(function($q) use ($user) {
                                   $q->whereNull('type_id') // allow NULL
                                     ->orWhereIn('type_id', $user->sport_type_access ?? []);
                               })
                               ->whereIn('division', $user->level_access ?? [])
                               ->where(function($q) use ($user) {
                                   $q->whereNull('category') // allow NULL
                                     ->orWhereIn('category', $user->category_access ?? []);
                               });
    }

    // GET RESULTS
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
<div class="p-6 mb-6 bg-white rounded-2xl shadow-sm border border-gray-100">

    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-amber-50 rounded-xl">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">Pending Awards Needing Approval</h3>
    </div>

    {{-- ================= ALL AWARDS ================= --}}
    @if($pendingResults->count())
        <div class="mb-8">
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                All Awards
            </h4>

            <div class="grid gap-4">
                @foreach($pendingResults as $r)
                    <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap mb-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $r->sport->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-sm text-gray-600">{{ $r->type->type_name ?? 'ALL EVENTS' }}</span>
                                </div>
                                <div class="flex flex-wrap gap-3 text-sm">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-gray-600">{{ $r->sdo }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                            @if($r->medal === 'Gold') bg-amber-100 text-amber-700
                                            @elseif($r->medal === 'Silver') bg-gray-100 text-gray-700
                                            @elseif($r->medal === 'Bronze') bg-orange-100 text-orange-700
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $r->medal }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                        </svg>
                                        <span class="text-gray-600 font-medium">{{ $r->count }} award(s)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- VALIDATION FILE --}}
                                @if($r->validation_file)
                                    @php
                                        $fileUrl = asset('storage/' . $r->validation_file);
                                        $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                        <a href="{{ $fileUrl }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition flex-shrink-0">
                                            <img src="{{ $fileUrl }}" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            View PDF
                                        </a>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm flex-shrink-0">—</span>
                                @endif

                                {{-- ACTIONS --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <form method="POST" action="{{ route('approve_all_awards') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="award_id" value="{{ $r->id }}">
                                        <button class="px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium rounded-lg transition">
                                            Approve
                                        </button>
                                    </form>
                                    <button @click="$dispatch('open-reject', { id: {{ $r->id }}, type: 'all' })" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ================= REJECT MODAL ================= --}}
    <div x-data="{ open:false, awardId:null, awardType:null, message:'' }"
         x-on:open-reject.window="
            open=true;
            awardId=$event.detail.id;
            awardType=$event.detail.type;
         ">

        <div x-show="open"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">

            <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-100 rounded-full">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Reject Award</h3>
                </div>

                <p class="text-sm text-gray-500 mb-4">Please provide a reason for rejection or revision instructions.</p>

                <textarea x-model="message"
                          class="w-full border border-gray-200 rounded-xl p-3 mb-5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                          placeholder="Enter revision message..."
                          rows="3"
                          required></textarea>

                <form method="POST"
                      :action="awardType === 'all'
                        ? '{{ route('reject_all_awards') }}'
                        : '{{ route('reject_individual_awards') }}'">

                    @csrf
                    <input type="hidden" name="award_id" :value="awardId">
                    <input type="hidden" name="message" :value="message">

                    <div class="flex justify-end gap-3">
                        <button type="button"
                                @click="open=false"
                                class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl transition shadow-sm">
                            Reject Award
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================= INDIVIDUAL AWARDS ================= --}}
    @if($pendingIndividual->count())
        <div>
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                Individual Awards
            </h4>

            <div class="grid gap-4">
                @foreach($pendingIndividual as $r)
                    <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap mb-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $r->sport->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">•</span>
                                    <span class="text-sm text-gray-600">{{ $r->type->type_name ?? '-' }}</span>
                                </div>
                                <div class="flex flex-wrap gap-3 text-sm mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-gray-600">{{ $r->sdo }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                            @if($r->medal === 'Gold') bg-amber-100 text-amber-700
                                            @elseif($r->medal === 'Silver') bg-gray-100 text-gray-700
                                            @elseif($r->medal === 'Bronze') bg-orange-100 text-orange-700
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $r->medal }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 text-sm">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $r->athlete ? $r->athlete->last_name . ', ' . $r->athlete->first_name : '-' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- VALIDATION FILE --}}
                                @if($r->validation_file)
                                    @php
                                        $fileUrl = asset('storage/' . $r->validation_file);
                                        $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                        <a href="{{ $fileUrl }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition flex-shrink-0">
                                            <img src="{{ $fileUrl }}" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            View PDF
                                        </a>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm flex-shrink-0">—</span>
                                @endif

                                {{-- ACTIONS --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button class="px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium rounded-lg transition" @click="$dispatch('open-modal', { awardId: {{ $r->id }}, type: 'individual' })">
                                        Approve
                                    </button>
                                    <button @click="$dispatch('open-reject', { id: {{ $r->id }}, type: 'individual' })" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($pendingResults->count() === 0 && $pendingIndividual->count() === 0)
        <div class="text-center py-10">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-gray-500">No pending awards to approve for your division/event.</p>
        </div>
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

@php
// ==============================
// 🚀 GROUP EVENTS (SUPER ADMIN: SPORT → TAB)
// ==============================

if ($user->role->name === 'Super Admin') {

    $groupedEvents = $events->groupBy('sport_id')->map(function ($sportEvents) {

        $sportName = optional($sportEvents->first()->sport)->name ?? 'Unknown Sport';

        return [
            'sport_name' => $sportName,

            'tabs' => $sportEvents->groupBy(function($event){
                if ($event->age_group === 'Paragames') {
                    return 'Paragames';
                }
                return $event->age_group . ' ' . $event->category;
            })->map(function ($eventsList) {

                return $eventsList
                    ->groupBy(function($event){
                        return $event->sport_id . '-' . $event->sport_type_id;
                    })
                    ->map(fn($group) => $group->first())
                    ->values();

            })
        ];

    });

} else {

    // ✅ ORIGINAL LOGIC (NON SUPER ADMIN)
    $groupedEvents = $events->groupBy(function($event){
        if ($event->age_group === 'Paragames') {
            return 'Paragames';
        }
        return $event->age_group . ' ' . $event->category;
    });

    $groupedEvents = $groupedEvents->map(function ($eventsList) {

        return $eventsList
            ->groupBy(function($event){
                return $event->sport_id . '-' . $event->sport_type_id;
            })
            ->map(fn($group) => $group->first())
            ->values();
    });
}

$divisions = \App\Models\Division::orderBy('name')->get();
@endphp


{{-- ================================================= --}}
{{-- 🏆 ALL AWARDS FORM --}}
{{-- ================================================= --}}
<div x-show="awardType==='all'">

@if(in_array($user->role->name, ['Super Admin','Tournament Secretary']))

<form method="POST"
      action="{{ route('results_all_awards.store') }}"
      enctype="multipart/form-data"
      x-data="{ showModal:false }">

@csrf

{{-- ================= SUPER ADMIN VIEW ================= --}}
@if($user->role->name === 'Super Admin')

    @foreach($groupedEvents as $sportGroup)

        {{-- 🏀 SPORT --}}
        <h1 class="text-3xl font-bold mt-10 mb-4 text-indigo-700">
            {{ strtoupper($sportGroup['sport_name']) }}
        </h1>

        @foreach($sportGroup['tabs'] as $label => $eventsList)

            {{-- 📂 CATEGORY --}}
            <h2 class="text-2xl font-bold mt-6 mb-3 text-blue-600">
                {{ strtoupper($label) }}
            </h2>

            @foreach($eventsList as $event)
                @php if (!is_object($event)) continue; @endphp

                <h3 class="font-semibold mt-4">
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
                            <td class="border px-2 font-medium">{{ $event->name }}</td>

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

        @endforeach

    @endforeach

{{-- ================= NON SUPER ADMIN ================= --}}
@else

    @foreach($groupedEvents as $label => $eventsList)

        <h2 class="text-2xl font-bold mt-8 mb-4 text-blue-700">
            {{ strtoupper($label) }}
        </h2>

        @foreach($eventsList as $event)
            @php if (!is_object($event)) continue; @endphp

            <h3 class="font-semibold mt-4">
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
                        <td class="border px-2 font-medium">{{ $event->name }}</td>

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

    @endforeach

@endif

{{-- ================= ACTION BUTTON ================= --}}
<div class="flex justify-end mt-6">
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

{{-- ================= MODAL ================= --}}
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

<form method="POST"
      action="{{ route('results_individual.store') }}"
      enctype="multipart/form-data">

@csrf

<input type="hidden" name="event_id" value="{{ $currentEvent?->id }}">

@php
    $athletes = \App\Models\Athlete::with('school')->orderBy('last_name')->get();
@endphp

{{-- ================= SUPER ADMIN VIEW ================= --}}
@if($user->role->name === 'Super Admin')

    @foreach($groupedEvents as $sportGroup)

        {{-- 🏀 SPORT --}}
        <h1 class="text-3xl font-bold mt-10 mb-4 text-green-700">
            {{ strtoupper($sportGroup['sport_name']) }}
        </h1>

        @foreach($sportGroup['tabs'] as $label => $eventsList)

            {{-- 📂 CATEGORY --}}
            <h2 class="text-2xl font-bold mt-6 mb-3 text-green-600">
                {{ strtoupper($label) }}
            </h2>

            @foreach($eventsList as $event)
                @php if (!is_object($event)) continue; @endphp

                <h3 class="font-semibold mt-4">
                    {{ strtoupper($event->name) }}
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
                            <td class="border px-2 font-medium">{{ $event->name }}</td>

                            @foreach(['Gold','Silver','Bronze'] as $medal)
                            <td class="border px-3 py-3 align-top">

                                <div x-data="{ selectedAthlete: null }">
                                    <input 
                                        list="athletes_list_{{ $event->id }}_{{ $medal }}"
                                        x-model="selectedAthlete"
                                        placeholder="Type athlete name..."
                                        class="w-full border rounded px-2 py-1 text-sm">

                                    <datalist id="athletes_list_{{ $event->id }}_{{ $medal }}">
                                        @foreach($athletes as $athlete)
                                            <option value="{{ $athlete->last_name }}, {{ $athlete->first_name }}"></option>
                                        @endforeach
                                    </datalist>

                                    <input type="hidden"
                                           :value="getAthleteId(selectedAthlete)"
                                           name="individual_results[{{ $event->id }}][{{ $medal }}][athlete]">

                                    <input type="hidden"
                                           :value="getAthleteSDO(selectedAthlete)"
                                           name="individual_results[{{ $event->id }}][{{ $medal }}][sdo]">

                                    <div class="text-xs text-gray-600 mt-1"
                                         x-html="getAthleteInfo(selectedAthlete)"></div>
                                </div>

                            </td>
                            @endforeach

                        </tr>
                    </tbody>
                </table>

            @endforeach

        @endforeach

    @endforeach

{{-- ================= NON SUPER ADMIN ================= --}}
@else

    @foreach($groupedEvents as $label => $eventsList)

        <h2 class="text-2xl font-bold mt-8 mb-4 text-green-700">
            {{ strtoupper($label) }}
        </h2>

        @foreach($eventsList as $event)
            @php if (!is_object($event)) continue; @endphp

            <h3 class="font-semibold mt-4">
                {{ strtoupper($event->name) }}
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
                        <td class="border px-2 font-medium">{{ $event->name }}</td>

                        @foreach(['Gold','Silver','Bronze'] as $medal)
                        <td class="border px-3 py-3 align-top">

                            <div x-data="{ selectedAthlete: null }">
                                <input 
                                    list="athletes_list_{{ $event->id }}_{{ $medal }}"
                                    x-model="selectedAthlete"
                                    placeholder="Type athlete name..."
                                    class="w-full border rounded px-2 py-1 text-sm">

                                <datalist id="athletes_list_{{ $event->id }}_{{ $medal }}">
                                    @foreach($athletes as $athlete)
                                        <option value="{{ $athlete->last_name }}, {{ $athlete->first_name }}"></option>
                                    @endforeach
                                </datalist>

                                <input type="hidden"
                                       :value="getAthleteId(selectedAthlete)"
                                       name="individual_results[{{ $event->id }}][{{ $medal }}][athlete]">

                                <input type="hidden"
                                       :value="getAthleteSDO(selectedAthlete)"
                                       name="individual_results[{{ $event->id }}][{{ $medal }}][sdo]">

                                <div class="text-xs text-gray-600 mt-1"
                                     x-html="getAthleteInfo(selectedAthlete)"></div>
                            </div>

                        </td>
                        @endforeach

                    </tr>
                </tbody>
            </table>

        @endforeach

    @endforeach

@endif

{{-- ================= SUBMIT BUTTON ================= --}}
<div class="flex justify-end mt-6">
    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">
        Save Individual Awards
    </button>
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