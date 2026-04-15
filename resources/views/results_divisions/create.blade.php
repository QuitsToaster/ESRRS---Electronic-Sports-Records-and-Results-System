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

<div 
    x-data="editAwardModal()" 
    class="p-6 mb-6 bg-white rounded-2xl shadow-sm border border-gray-100">

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
                                <button
                                    type="button"
                                    @click.stop="
openEditModal(
    {{ $r->id }},
    'all',
    '{{ $r->medal }}',
    '{{ $r->sdo }}'
)
"
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm flex-shrink-0">
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
                                <button
                                    type="button"
                                   @click.stop="
openEditModal(
    {{ $r->id }},
    'individual',
    '{{ $r->medal }}',
    '{{ $r->sdo }}'
)
"
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm flex-shrink-0">
                                    Edit
                                </button>
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
        athletes: [],
        filtered: [],
        loading: false,
        selectedAthlete: null,

        // ✅ RENAMED FUNCTION (IMPORTANT)
        openEditModal(id, type, medal, sdo) {

            // ✅ FORCE STOP GLOBAL CONFLICT
            this.show = false;

            this.awardId = id;
            this.type = type;
            this.medal = medal;
            this.sdo = sdo;

            this.search = '';
            this.filtered = [];
            this.selectedAthlete = null;

            // ✅ FORCE OPEN AFTER RESET
            setTimeout(() => {
                this.show = true;
            }, 10);
        },

        async filterAthletes() {
            let q = this.search;

            if (q.length < 2) {
                this.filtered = [];
                return;
            }

            this.loading = true;

            let res = await fetch(`/ajax/athletes?q=${q}`);
            let data = await res.json();

            this.filtered = data.map(a => ({
                id: a.id,
                name: a.name,
                sdo: 'SDO - ' + a.sdo
            }));

            this.loading = false;
        },

        selectAthlete(ath) {
            this.selectedAthlete = ath;
            this.search = ath.name;
            this.sdo = ath.sdo;
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

@php
    $chunkSize = 10; // items per page
    $allAwardChunks = $pendingResults->chunk($chunkSize)->values();
@endphp

<div x-data="{ page: 0 }" class="mb-8">

    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
        All Awards
    </h4>

    {{-- ================= PAGE BUTTONS ================= --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach($allAwardChunks as $index => $chunk)
            <button
                @click="page = {{ $index }}"
                :class="page === {{ $index }} 
                    ? 'bg-amber-500 text-white' 
                    : 'bg-gray-100 text-gray-600'"
                class="px-3 py-1 text-xs rounded-lg font-medium transition">
                Page {{ $index + 1 }}
            </button>
        @endforeach
    </div>

    {{-- ================= DATA ================= --}}
    @foreach($allAwardChunks as $index => $chunk)
        <div x-show="page === {{ $index }}" class="grid gap-4">

            @foreach($chunk as $r)
            <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
                <div class="flex flex-wrap items-start justify-between gap-4">

                    <div class="flex-1 min-w-0">

                        {{-- HEADER --}}
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <span class="text-sm font-semibold text-gray-900">{{ $r->sport->name ?? 'N/A' }}</span>
                            <span class="text-xs text-gray-500">•</span>
                            <span class="text-sm text-gray-600">{{ $r->type->type_name ?? 'ALL EVENTS' }}</span>
                        </div>

                        {{-- LABELS --}}
                        <div class="flex gap-2 mb-2">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($r->division === 'Elementary') bg-blue-100 text-blue-700
                                @elseif($r->division === 'Secondary') bg-purple-100 text-purple-700
                                @elseif($r->division === 'Paragames') bg-green-100 text-green-700
                                @endif">
                                {{ $r->division }}
                            </span>

                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($r->category === 'Boys') bg-blue-200 text-blue-800
                                @elseif($r->category === 'Girls') bg-pink-200 text-pink-800
                                @elseif($r->category === 'Mixed') bg-gray-200 text-gray-800
                                @endif">
                                {{ $r->category ?? 'Mixed' }}
                            </span>
                        </div>

                        {{-- DETAILS --}}
                        <div class="flex flex-wrap gap-3 text-sm">
                            <span>{{ $r->sdo }}</span>

                            <span class="px-2 py-0.5 rounded-full text-xs
                                @if($r->medal === 'Gold') bg-amber-100 text-amber-700
                                @elseif($r->medal === 'Silver') bg-gray-100 text-gray-700
                                @elseif($r->medal === 'Bronze') bg-orange-100 text-orange-700
                                @endif">
                                {{ $r->medal }}
                            </span>

                            <span>{{ $r->count }} award(s)</span>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('approve_all_awards') }}">
                            @csrf
                            <input type="hidden" name="award_id" value="{{ $r->id }}">
                            <button class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg">
                                Approve
                            </button>
                        </form>

                        <button @click="$dispatch('open-reject', { id: {{ $r->id }}, type: 'all' })"
                                class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg">
                            Reject
                        </button>
                    </div>

                </div>
            </div>
            @endforeach

        </div>
    @endforeach

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

@php
    $chunkSize = 10;
    $individualChunks = $pendingIndividual->chunk($chunkSize)->values();
@endphp

<div x-data="{ page: 0 }">

    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">
        Individual Awards
    </h4>

    {{-- PAGE BUTTONS --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach($individualChunks as $index => $chunk)
            <button
                @click="page = {{ $index }}"
                :class="page === {{ $index }} 
                    ? 'bg-blue-500 text-white' 
                    : 'bg-gray-100 text-gray-600'"
                class="px-3 py-1 text-xs rounded-lg font-medium">
                Page {{ $index + 1 }}
            </button>
        @endforeach
    </div>

    {{-- DATA --}}
    @foreach($individualChunks as $index => $chunk)
        <div x-show="page === {{ $index }}" class="grid gap-4">

            @foreach($chunk as $r)
            <div class="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">

                <div class="flex justify-between gap-4">

                    <div class="flex-1">

                        <div class="flex gap-2 mb-2">
                            <span class="font-semibold">{{ $r->sport->name }}</span>
                            <span>•</span>
                            <span>{{ $r->type->type_name }}</span>
                        </div>

                        <div class="flex gap-2 mb-2">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                {{ $r->division }}
                            </span>

                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-800">
                                {{ $r->category ?? 'Mixed' }}
                            </span>
                        </div>

                        <div class="text-sm">
                            {{ $r->athlete?->last_name }},
{{ $r->athlete?->first_name }}
{{ $r->athlete?->middle_initial ? $r->athlete->middle_initial . '.' : '' }}
                        </div>

                        <div class="text-sm">{{ $r->sdo }}</div>

                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($r->medal === 'Gold') bg-amber-100 text-amber-700
                            @elseif($r->medal === 'Silver') bg-gray-100 text-gray-700
                            @elseif($r->medal === 'Bronze') bg-orange-100 text-orange-700
                            @endif">
                            {{ $r->medal }}
                        </span>

                    </div>

                    <div class="flex gap-2">
                        <button @click="$dispatch('open-modal', { awardId: {{ $r->id }}, type: 'individual' })"
                                class="px-3 py-1.5 bg-green-50 text-green-700 rounded">
                            Approve
                        </button>

                        <button @click="$dispatch('open-reject', { id: {{ $r->id }}, type: 'individual' })"
                                class="px-3 py-1.5 bg-red-50 text-red-700 rounded">
                            Reject
                        </button>
                    </div>

                </div>

            </div>
            @endforeach

        </div>
    @endforeach

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
// =====================================================
// 🚀 NEW: SPORT → TYPE (NO EVENT DEPENDENCY)
// =====================================================


$query = Sport::with(['types']) // make sure relation exists
    ->orderBy('name');

// ================= ROLE FILTER =================
if ($user->role->name !== 'Super Admin') {
    $query->whereIn('id', $user->sports_access ?? []);
}

// ================= PAGINATION =================
$sports = $query->paginate(10); // adjust per page if needed

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

{{-- ================= UNIVERSAL VIEW ================= --}}
@foreach($sports as $sport)

    {{-- 🏀 SPORT --}}
    <div class="mt-10 mb-4">

        {{-- SPORT TITLE --}}
        <h1 class="text-3xl font-bold text-indigo-700">
            {{ strtoupper($sport->name) }}
        </h1>

        {{-- ✅ NEW: SPORT LABEL (FROM DB) --}}
        <div class="mt-2">
            <span class="text-xs px-3 py-1 rounded-full font-medium
                @if($sport->division === 'Elementary') bg-blue-100 text-blue-700
                @elseif($sport->division === 'Secondary') bg-purple-100 text-purple-700
                @elseif($sport->division === 'Paragames') bg-green-100 text-green-700
                @else bg-gray-100 text-gray-700
                @endif
            ">
                {{ $sport->division ?? 'N/A' }}
            </span>

            <span class="text-xs px-3 py-1 rounded-full font-medium ml-1
                @if($sport->category === 'Boys') bg-blue-200 text-blue-800
                @elseif($sport->category === 'Girls') bg-pink-200 text-pink-800
                @elseif($sport->category === 'Mixed') bg-gray-200 text-gray-800
                @else bg-gray-100 text-gray-700
                @endif
            ">
                {{ $sport->category ?? 'Mixed' }}
            </span>
        </div>

    </div>

    {{-- ================= TYPES ================= --}}
    @forelse($sport->types as $type)

        <h2 class="text-xl font-semibold mt-4 mb-2 text-blue-600">
            {{ strtoupper($type->type_name) }}
        </h2>

        {{-- ================= TABLE ================= --}}
        <table class="w-full border text-sm mt-2">
            <thead class="bg-gray-200 text-center">
                <tr>
                    <th class="border px-2 py-2">Sport</th>
                    <th class="border bg-yellow-300">Gold</th>
                    <th class="border bg-gray-300">Silver</th>
                    <th class="border bg-orange-200">Bronze</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="border px-2 font-medium">
                        <div>
                            <div class="font-semibold">
                                {{ $sport->name }} - {{ $type->type_name }}
                            </div>

                            {{-- ✅ REPEAT LABEL INSIDE TABLE --}}
                            <div class="mt-1 flex gap-1">
                                <span class="text-[10px] px-2 py-0.5 rounded
                                    @if($sport->division === 'Elementary') bg-blue-100 text-blue-700
                                    @elseif($sport->division === 'Secondary') bg-purple-100 text-purple-700
                                    @elseif($sport->division === 'Paragames') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ $sport->division }}
                                </span>

                                <span class="text-[10px] px-2 py-0.5 rounded
                                    @if($sport->category === 'Boys') bg-blue-200 text-blue-800
                                    @elseif($sport->category === 'Girls') bg-pink-200 text-pink-800
                                    @elseif($sport->category === 'Mixed') bg-gray-200 text-gray-800
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ $sport->category ?? 'Mixed' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    @foreach(['Gold','Silver','Bronze'] as $medal)
                    <td class="border px-2 py-2 align-top">
    @php
        $divisionValue = in_array($sport->division, ['Elementary', 'Secondary', 'Paragames'])
            ? $sport->division
            : '';

        $categoryValue = $sport->category;
    @endphp

    <div x-data="divisionDropdown()">

    <template x-for="(entry, index) in entries" :key="index">
        <div class="flex gap-2 mb-2 items-start">

            <select
                x-model="entry.sdo"
                @click="loadDivisions(); $event.target.size = divisions.length ? divisions.length : 5"
@blur="$event.target.size = 1"
                :name="'results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}][' + index + '][sdo]'"
                class="w-full border rounded px-2 py-1 text-sm"
            >
                <option value="">-- Select Division / SDO --</option>

                <template x-for="d in divisions" :key="d">
                    <option :value="d" x-text="d"></option>
                </template>
            </select>

            {{-- HIDDEN --}}
            <input type="hidden"
                :name="'results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}][' + index + '][division]'"
                value="{{ $divisionValue }}">

            <input type="hidden"
                :name="'results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}][' + index + '][category]'"
                value="{{ $categoryValue }}">

            <button type="button"
                x-show="entries.length > 1"
                @click="entries.splice(index, 1)"
                class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                ✕
            </button>

        </div>
    </template>

    <button type="button"
        @click="entries.push({ sdo: '' })"
        class="text-blue-600 text-xs hover:underline">
        + Add Division
    </button>

</div>
</td>
                    @endforeach

                </tr>
            </tbody>
        </table>

    @empty
        <p class="text-gray-500 text-sm">No sport types available.</p>
    @endforelse

@endforeach

{{-- ================= PAGINATION ================= --}}
<div class="mt-6">
    {{ $sports->withPath(url()->current())->links() }}
</div>

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


{{-- ================= UNIVERSAL VIEW ================= --}}
@foreach($sports as $sport)

    {{-- 🏀 SPORT --}}
    <div class="mt-10 mb-4">

        <h1 class="text-3xl font-bold text-green-700">
            {{ strtoupper($sport->name) }}
        </h1>

        {{-- ✅ NEW: SPORT LABEL --}}
        <div class="mt-2">
            <span class="text-xs px-3 py-1 rounded-full font-medium
                @if($sport->division === 'Elementary') bg-blue-100 text-blue-700
                @elseif($sport->division === 'Secondary') bg-purple-100 text-purple-700
                @elseif($sport->division === 'Paragames') bg-green-100 text-green-700
                @else bg-gray-100 text-gray-700
                @endif
            ">
                {{ $sport->division ?? 'N/A' }}
            </span>

            <span class="text-xs px-3 py-1 rounded-full font-medium ml-1
                @if($sport->category === 'Boys') bg-blue-200 text-blue-800
                @elseif($sport->category === 'Girls') bg-pink-200 text-pink-800
                @elseif($sport->category === 'Mixed') bg-gray-200 text-gray-800
                @else bg-gray-100 text-gray-700
                @endif
            ">
                {{ $sport->category ?? 'Mixed' }}
            </span>
        </div>

    </div>

    {{-- ================= TYPES ================= --}}
    @forelse($sport->types as $type)

        <h2 class="text-xl font-semibold mt-4 mb-2 text-green-600">
            {{ strtoupper($type->type_name) }}
        </h2>

        {{-- ================= TABLE ================= --}}
        <table class="w-full border text-sm mt-2">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-2">Sport</th>
                    <th class="border px-2 py-2 bg-yellow-100">Gold</th>
                    <th class="border px-2 py-2 bg-gray-200">Silver</th>
                    <th class="border px-2 py-2 bg-orange-200">Bronze</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="border px-2 font-medium">
                        <div>
                            <div class="font-semibold">
                                {{ $sport->name }} - {{ $type->type_name }}
                            </div>

                            {{-- ✅ LABEL INSIDE TABLE --}}
                            <div class="mt-1 flex gap-1">
                                <span class="text-[10px] px-2 py-0.5 rounded
                                    @if($sport->division === 'Elementary') bg-blue-100 text-blue-700
                                    @elseif($sport->division === 'Secondary') bg-purple-100 text-purple-700
                                    @elseif($sport->division === 'Paragames') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ $sport->division }}
                                </span>

                                <span class="text-[10px] px-2 py-0.5 rounded
                                    @if($sport->category === 'Boys') bg-blue-200 text-blue-800
                                    @elseif($sport->category === 'Girls') bg-pink-200 text-pink-800
                                    @elseif($sport->category === 'Mixed') bg-gray-200 text-gray-800
                                    @else bg-gray-100 text-gray-700
                                    @endif
                                ">
                                    {{ $sport->category ?? 'Mixed' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    @foreach(['Gold','Silver','Bronze'] as $medal)
                    <td class="border px-3 py-3 align-top">

                     <div x-data="individualAwardsForm()">

    <template x-for="(ath,index) in athletes" :key="index">
        <div class="border rounded p-2 mb-2 bg-gray-50">

            {{-- ✅ SINGLE INPUT ONLY --}}
            <input 
                type="text"
                x-model="ath.name"
                @input.debounce.400ms="searchAthletes(index)"
                placeholder="Type athlete name..."
                class="w-full border rounded px-2 py-1 text-sm">

            {{-- ✅ RESULTS --}}
            <div x-show="ath.results.length"
                 class="border mt-1 rounded bg-white max-h-40 overflow-y-auto">

                <template x-for="item in ath.results" :key="item.id">
                    <div @click="selectAthlete(index, item)"
                         class="px-2 py-1 hover:bg-gray-100 cursor-pointer">
                        <span x-text="item.name"></span>
                        <span class="text-xs text-gray-500 ml-2"
                              x-text="'(' + item.sdo + ')'"></span>
                    </div>
                </template>
            </div>

            {{-- ✅ HIDDEN VALUES --}}
            <input type="hidden"
                :value="ath.selected ? ath.selected.id : ''"
                :name="'individual_results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}]['+index+'][athlete]'">

            <input type="hidden"
                :value="ath.selected ? ath.selected.sdo : ''"
                :name="'individual_results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}]['+index+'][sdo]'">

            <input type="hidden"
                :name="'individual_results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}]['+index+'][division]'"
                value="{{ $sport->division }}">

            <input type="hidden"
                :name="'individual_results[{{ $sport->id }}][{{ $type->id ?? 0 }}][{{ $medal }}]['+index+'][category]'"
                value="{{ $sport->category }}">

            {{-- INFO --}}
            <div class="text-xs text-gray-600 mt-1"
                 x-text="ath.selected ? ath.selected.sdo : ''"></div>

            {{-- REMOVE --}}
            <button type="button"
                    x-show="athletes.length > 1"
                    @click="athletes.splice(index,1)"
                    class="mt-1 text-xs bg-red-500 text-white px-2 py-1 rounded">
                Remove
            </button>

        </div>
    </template>

    {{-- ADD --}}
    <button type="button"
            @click="athletes.push({name:'', results:[], selected:null})"
            class="text-green-600 text-xs hover:underline">
        + Add Athlete
    </button>

</div>

                    </td>
                    @endforeach

                </tr>
            </tbody>
        </table>

    @empty
        <p class="text-gray-500 text-sm">No sport types available.</p>
    @endforelse

@endforeach

{{-- ================= PAGINATION ================= --}}
<div class="mt-6">
    {{ $sports->withPath(url()->current())->links() }}
</div>

{{-- ================= SUBMIT ================= --}}
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

        athletes: [{ name:'', results:[], selected:null }],
        cache: {},

        async searchAthletes(index) {

            let athlete = this.athletes[index];
            let q = athlete.name;

            if (!q || q.length < 2) {
                athlete.results = [];
                return;
            }

            // cache
            if (this.cache[q]) {
                athlete.results = this.cache[q];
                return;
            }

            try {
                let res = await fetch(`{{ url('/ajax/athletes') }}?q=${encodeURIComponent(q)}`);
                let data = await res.json();

                // ✅ FIX: BUILD NAME MANUALLY
                let formatted = data.map(a => {

                    let last = a.last_name ?? '';
                    let first = a.first_name ?? '';
                    let middle = a.middle_initial ? a.middle_initial + '.' : '';

                    let fullName = `${last}, ${first} ${middle}`.trim();

                    return {
                        id: a.id,
                        name: fullName,
                        sdo: 'SDO - ' + a.sdo
                    };
                });

                this.cache[q] = formatted;
                athlete.results = formatted;

            } catch (e) {
                console.error('Athlete search error:', e);
            }
        },

        selectAthlete(index, item) {
            this.athletes[index].name = item.name;
            this.athletes[index].selected = item;
            this.athletes[index].results = [];
        }
    }
}
</script>


</div>

<!-- <script>
function awardApp(){
    return {
        activeTab:'{{ array_key_first($tabs) }}',
        awardType:'all',
        individualResults:@json($prefillIndividual),
    }
}
</script> -->
<script>
function awardApp(){
    return {
        activeTab:'{{ array_key_first($tabs) }}',
        awardType:'all',
        individualResults:@json($prefillIndividual),

        divisions: [],
        loadedDivisions: false,

        async loadDivisions() {
            if (this.loadedDivisions) return;

            try {
                let res = await fetch("{{ url('/ajax/divisions') }}");
                let data = await res.json();

                this.divisions = data;
                this.loadedDivisions = true;

            } catch (e) {
                console.error('Division fetch error:', e);
            }
        }
    }
}

function divisionDropdown() {
    return {
        entries: [{ sdo: '' }],
        divisions: [],
        loaded: false,

        async loadDivisions() {
            if (this.loaded) return;

            try {
                let res = await fetch("{{ url('/ajax/divisions') }}");
                let data = await res.json();

                this.divisions = data;
                this.loaded = true;

            } catch (e) {
                console.error('Division dropdown error:', e);
            }
        }
    }
}

/* ================= INDIVIDUAL SEARCH FIX ================= */
function individualAwardsForm() {
    return {

        athletes: [{ name:'', results:[], selected:null }],
        cache: {},

        async searchAthletes(index) {

            let athlete = this.athletes[index];
            let q = athlete.name;

            if (!q || q.length < 2) {
                athlete.results = [];
                return;
            }

            // cache
            if (this.cache[q]) {
                athlete.results = this.cache[q];
                return;
            }

            try {
                let res = await fetch(`{{ url('/ajax/athletes') }}?q=${encodeURIComponent(q)}`);
                let data = await res.json();

                let formatted = data.map(a => ({
                    id: a.id,
                    name: a.name,
                    sdo: 'SDO - ' + a.sdo
                }));

                this.cache[q] = formatted;
                athlete.results = formatted;

            } catch (e) {
                console.error('Athlete search error:', e);
            }
        },

        selectAthlete(index, item) {
            this.athletes[index].name = item.name;
            this.athletes[index].selected = item;
            this.athletes[index].results = [];
        }
    }
}
</script>

@endsection