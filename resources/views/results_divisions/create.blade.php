@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\Athlete;
use App\Models\Event; 

$user = auth()->user();

// ====================== FILTER TABS FOR TOURNAMENT SECRETARY ======================
$allTabs = [
    'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
    'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
    'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
    'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
    'Para Games'        => ['division' => 'Paragames', 'category' => null],
];

// If not Super Admin, filter tabs based on user's level_access and category_access
if ($user->role->name !== 'Super Admin') {
    $tabs = array_filter($allTabs, function($tab) use ($user) {
        $levelOk = in_array($tab['division'], $user->level_access);
        $categoryOk = is_null($tab['category']) || in_array($tab['category'], $user->category_access);
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

// ====================== ALL ATHLETES ======================


// ================= PREFILL ALL AWARDS RESULTS (ONLY APPROVED) =================
$prefillResults = [];
foreach ($tabs as $tabName => $tab) {
    $query = AllAwardResult::where('division', $tab['division'])
        ->where('status', 'approved'); // <-- only approved awards

    if (!is_null($tab['category'])) {
        $query->where('category', $tab['category']);
    } else {
        $query->whereNull('category');
    }

    if ($user->role->name !== 'Super Admin') {
        $query->whereIn('sport_id', $user->sports_access)
              ->where('sdo', $user->division_name);
    }

    foreach ($query->get() as $r) {
        $prefillResults[$tabName][$r->sport_id][$r->type_id ?? 0][$r->sdo][$r->medal] = $r->count;
    }
}

// ================= PREFILL INDIVIDUAL AWARDS (ONLY APPROVED) =================
$prefillIndividual = [];
foreach ($tabs as $tabName => $tab) {
    $query = IndividualAwardResult::where('division', $tab['division'])
        ->where('status', 'approved'); // <-- only approved awards

    if (!is_null($tab['category'])) {
        $query->where('category', $tab['category']);
    } else {
        $query->whereNull('category');
    }

    if ($user->role->name !== 'Super Admin') {
        $query->whereIn('sport_id', $user->sports_access)
              ->where('sdo', $user->division_name);
    }

    foreach ($query->get() as $r) {
        $prefillIndividual[$tabName][$r->sport_id][$r->type_id ?? 0][$r->sdo][$r->medal] = $r->athlete_id;
    }
}


// ================= PENDING AWARDS FOR TOURNAMENT MANAGER / SUPER ADMIN =================
$pendingResults = collect();
$pendingIndividual = collect();

if ($user->role->name === 'Tournament Manager' || $user->role->name === 'Super Admin') {
    $activeEventId = Event::where('is_active', 1)->value('id');

    if ($activeEventId) {
        $pendingResultsQuery = AllAwardResult::where('status','pending')
            ->where('event_id', $activeEventId);

        $pendingIndividualQuery = IndividualAwardResult::where('status','pending')
            ->where('event_id', $activeEventId);

        // Apply filters only if not Super Admin
        if ($user->role->name !== 'Super Admin') {
            $pendingResultsQuery->where('sdo', $user->division_name)
                                ->whereIn('sport_id', $user->sports_access);

            $pendingIndividualQuery->where('sdo', $user->division_name)
                                   ->whereIn('sport_id', $user->sports_access);
        }

        $pendingResults = $pendingResultsQuery->get();
        $pendingIndividual = $pendingIndividualQuery->get();
    }
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

{{-- ================= PENDING AWARDS FOR TOURNAMENT SECRETARY ================= --}}
@if($user->role->name === 'Tournament Secretary')
@php
    $activeEventId = \App\Models\Event::where('is_active', 1)->value('id');

    $pendingResultsSecretary = collect();
    $pendingIndividualSecretary = collect();

    if ($activeEventId) {
        $pendingResultsSecretary = \App\Models\AllAwardResult::where('status','needs_revision')
            ->where('event_id', $activeEventId)
            ->where('sdo', $user->division_name)
            ->whereIn('sport_id', $user->sports_access)
            ->get();

        $pendingIndividualSecretary = \App\Models\IndividualAwardResult::where('status','needs_revision')
            ->where('event_id', $activeEventId)
            ->where('sdo', $user->division_name)
            ->whereIn('sport_id', $user->sports_access)
            ->get();
    }
@endphp

<div class="p-4 mb-6 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded shadow-sm">
    <h3 class="font-semibold mb-2">Pending Awards Needing Revision</h3>

    {{-- ================= ALL AWARDS ================= --}}
@if($pendingResultsSecretary->count())
    <h4 class="font-semibold mt-2 mb-1">All Awards</h4>

    <table class="w-full border text-sm mb-4">
        <thead class="bg-yellow-200 text-center">
            <tr>
                <th class="border px-2">Sport</th>
                <th class="border px-2">Event Type</th>
                <th class="border px-2">Medal</th>
                <th class="border px-2">Count</th>
                <th class="border px-2">Validation File</th>
                <th class="border px-2">Action</th> {{-- New column --}}
            </tr>
        </thead>

        <tbody>
            @foreach($pendingResultsSecretary as $r)
                <tr class="text-center">
                    <td class="border px-2">{{ $r->sport->name ?? 'N/A' }}</td>
                    <td class="border px-2">{{ $r->type->type_name ?? '-' }}</td>
                    <td class="border px-2">{{ $r->medal }}</td>
                    <td class="border px-2">{{ $r->count }}</td>
                    <td class="border px-2">
                        @if($r->validation_file)
                            @php
                                $fileUrl = asset('storage/' . $r->validation_file);
                                $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $fileUrl }}" target="_blank">
                                    <img src="{{ $fileUrl }}" class="h-12 mx-auto rounded shadow hover:scale-105 transition">
                                </a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 underline font-semibold">View PDF</a>
                            @endif
                        @else
                            <span class="text-gray-400 italic">No file</span>
                        @endif
                    </td>
                    <td class="border px-2">
                        <form action="{{ route('results_all_awards.revise', $r->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revise this award? It will be deleted.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Revise</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- ================= INDIVIDUAL AWARDS ================= --}}
@if($pendingIndividualSecretary->count())
    <h4 class="font-semibold mt-2 mb-1">Individual Awards</h4>

    <table class="w-full border text-sm">
        <thead class="bg-yellow-200 text-center">
            <tr>
                <th class="border px-2">Sport</th>
                <th class="border px-2">Event Type</th>
                <th class="border px-2">Medal</th>
                <th class="border px-2">Athlete</th>
                <th class="border px-2">Validation File</th>
                <th class="border px-2">Action</th> {{-- New column --}}
            </tr>
        </thead>

        <tbody>
            @foreach($pendingIndividualSecretary as $r)
                <tr class="text-center">
                    <td class="border px-2">{{ $r->sport->name ?? 'N/A' }}</td>
                    <td class="border px-2">{{ $r->type->type_name ?? '-' }}</td>
                    <td class="border px-2">{{ $r->medal }}</td>
                    <td class="border px-2">
                        @php $ath = $athletes->find($r->athlete_id); @endphp
                        {{ $ath ? $ath->last_name . ', ' . $ath->first_name : '-' }}
                    </td>
                    <td class="border px-2">
                        @if($r->validation_file)
                            @php
                                $fileUrl = asset('storage/' . $r->validation_file);
                                $ext = strtolower(pathinfo($r->validation_file, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $fileUrl }}" target="_blank">
                                    <img src="{{ $fileUrl }}" class="h-12 mx-auto rounded shadow hover:scale-105 transition" alt="Validation Image">
                                </a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" class="text-blue-600 underline font-semibold">View PDF</a>
                            @endif
                        @else
                            <span class="text-gray-400 italic">No file</span>
                        @endif
                    </td>
                    <td class="border px-2">
                        <form action="{{ route('results_individual.revise', $r->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revise this award? It will be deleted.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Revise</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

    @if($pendingResultsSecretary->count() === 0 && $pendingIndividualSecretary->count() === 0)
        <p>No pending awards requiring revision for your division.</p>
    @endif
</div>
@endif

{{-- ================= PENDING AWARDS FOR TOURNAMENT MANAGER / SUPER ADMIN ================= --}}
@php

$pendingResults = collect();
$pendingIndividual = collect();

if ($user->role->name === 'Tournament Manager' || $user->role->name === 'Super Admin') {
    $activeEventId = Event::where('is_active', 1)->value('id');

    if ($activeEventId) {

        // All Awards Pending
        $pendingResultsQuery = AllAwardResult::where('status','pending')
            ->where('event_id', $activeEventId);

        // Individual Awards Pending
        $pendingIndividualQuery = IndividualAwardResult::where('status','pending')
            ->where('event_id', $activeEventId);

        if ($user->role->name !== 'Super Admin') {
            // Filter by user's access
            $pendingResultsQuery->whereIn('sport_id', $user->sports_access)
                                ->whereIn('type_id', $user->sport_type_access ?? [])
                                ->whereIn('division', $user->level_access)
                                ->where(function($q) use ($user) {
                                    $q->whereNull('category')
                                      ->orWhereIn('category', $user->category_access ?? []);
                                });

            $pendingIndividualQuery->whereIn('sport_id', $user->sports_access)
                                   ->whereIn('type_id', $user->sport_type_access ?? [])
                                   ->whereIn('division', $user->level_access)
                                   ->where(function($q) use ($user) {
                                        $q->whereNull('category')
                                          ->orWhereIn('category', $user->category_access ?? []);
                                   });
        }

        $pendingResults = $pendingResultsQuery->get();
        $pendingIndividual = $pendingIndividualQuery->get();
    }
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
                        <td class="border px-2">{{ $r->type->type_name ?? '-' }}</td>
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
                            @php $ath = $athletes->find($r->athlete_id); @endphp
                            {{ $ath ? $ath->last_name . ', ' . $ath->first_name : '-' }}
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

                        <td class="border px-2">
                            <button
                                class="bg-green-500 text-white px-3 py-1 rounded"
                                @click="$dispatch('open-modal', { awardId: {{ $r->id }}, type: 'individual' })">
                                Approve
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
// -----------------------------
// SPORTS QUERY WITH HAS(types) FILTER
// -----------------------------
$sportsQuery = \App\Models\Sport::with(['types' => function ($q) use ($user) {
    // 🔒 FILTER SPORT TYPES FOR TOURNAMENT SECRETARY
    if ($user->role->name === 'Tournament Secretary' && !empty($user->sport_type_access)) {
        $q->whereIn('id', $user->sport_type_access);
    }
}])
->where('division', $tab['division']);

// 🔒 FILTER SPORTS FOR NON–SUPER ADMIN
if ($user->role->name !== 'Super Admin') {
    $sportsQuery->whereIn('id', $user->sports_access ?? []);
}

// ✅ Only include sports that have at least one allowed type
$sportsQuery->whereHas('types', function($q) use ($user) {
    if ($user->role->name === 'Tournament Secretary' && !empty($user->sport_type_access)) {
        $q->whereIn('id', $user->sport_type_access);
    }
});

$sportsQuery->orderBy('name');

// ✅ PAGINATE safely — now only sports with types are included
$sports = $sportsQuery->paginate(10);

// -----------------------------
// FILTER ATHLETES FOR THIS TAB
// -----------------------------
$tabAthletes = $athletes->filter(fn($a) =>
    $a->division == $tab['division'] &&
    ($tab['category'] ? $a->category == $tab['category'] : true) &&
    ($user->role->name === 'Super Admin' || $a->sdo == $user->division_name)
)->sortBy('last_name');
@endphp

        <div x-show="activeTab==='{{ $tabName }}'" x-cloak>

            <h2 class="text-xl font-bold mb-4">{{ $tabName }}</h2>

            {{-- ========================================================= --}}
            {{-- 🏆 ALL AWARDS TABLE --}}
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

{{-- PAGINATION LINKS OUTSIDE TABLE --}}
<div class="mt-6">
    {{ $sports->links('pagination::tailwind') }}
</div>
            </div>

            
{{-- ================================================= --}}
{{-- 🏆 ALL AWARDS FORM (UPDATED WITH DIVISION SELECT) --}}
{{-- ================================================= --}}
<form method="POST"
      action="{{ route('results_all_awards.store') }}"
      enctype="multipart/form-data"
      x-show="awardType==='all'"
      x-data="{ showConfirm:false }">

@csrf

<input type="hidden" name="division" value="{{ $tab['division'] }}">
<input type="hidden" name="category" value="{{ $tab['category'] ?? '' }}">

{{-- 🚫 READ ONLY NOTICE --}}
@if($user->role->name === 'Tournament Manager')
<div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
    <strong>Read Only:</strong>
    Tournament Managers cannot modify or submit Awards.
</div>
@endif

{{-- ================= DIVISION SELECTOR ================= --}}
<div class="mb-6 flex items-center gap-4">
    <div>
        <label class="block text-sm font-semibold mb-1">
            Select Division (SDO)
        </label>
        <select name="selected_sdo"
                class="border rounded px-3 py-2 w-64"
                {{ $user->role->name === 'Tournament Manager' ? 'disabled' : 'required' }}>
            <option value="">-- Choose Division --</option>
            @foreach(\App\Models\Division::orderBy('name')->get() as $divisionOption)
                <option value="{{ $divisionOption->name }}"
                    {{ $tab['division'] === $divisionOption->name ? 'selected' : '' }}>
                    {{ $divisionOption->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="text-sm text-gray-600 mt-6">
        <strong>Note:</strong> Enter <span class="font-bold text-green-600">1</span> on Gold, Silver, or Bronze to forward.
    </div>
</div>

{{-- ================= SPORTS TABLE ================= --}}
@php
    if($user->role->name === 'Tournament Manager') {
        // Decode sports_access JSON to array of IDs
        $accessibleSportIds = is_string($user->sports_access) ? json_decode($user->sports_access, true) : $user->sports_access;
        $accessibleSports = \App\Models\Sport::whereIn('id', $accessibleSportIds)->get();
        $accessibleTypeIds = is_string($user->sport_type_access) ? json_decode($user->sport_type_access, true) : $user->sport_type_access;
    } else {
        $accessibleSports = $sports;
        $accessibleTypeIds = null;
    }
@endphp

@foreach($accessibleSports as $sport)
    <h3 class="font-semibold mt-6">
        {{ strtoupper($sport->name) }}
        @if(strtolower($sport->division) === 'paragames')
            — {{ $sport->category ?? 'N/A' }}
        @endif
    </h3>

    <table class="w-full border text-sm mt-2
        {{ $user->role->name === 'Tournament Manager' ? 'opacity-60 pointer-events-none' : '' }}">
        <thead class="bg-gray-200 text-center">
            <tr>
                <th class="border">Event Type</th>
                <th class="border bg-yellow-300">Gold</th>
                <th class="border bg-gray-300">Silver</th>
                <th class="border bg-orange-200">Bronze</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sport->types->whereIn('id', $accessibleTypeIds ?? $sport->types->pluck('id')->toArray()) as $type)
                <tr>
                    <td class="border px-2">{{ $type->type_name }}</td>
                    @foreach(['Gold','Silver','Bronze'] as $medal)
                        <td class="border text-center">
                            <input type="number"
                                   min="0"
                                   max="1"
                                   value="0"
                                   class="w-16 border rounded text-center"
                                   name="results[{{ $sport->id }}][{{ $type->id }}][__SDO__][{{ $medal }}]"
                                   oninput="this.value = this.value > 1 ? 1 : this.value"
                                   {{ $user->role->name === 'Tournament Manager' ? 'disabled' : '' }}>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

{{-- ================= ACTION BUTTONS ================= --}}
<div class="flex justify-end mt-6">

@if($user->role->name === 'Tournament Secretary')
    <button type="button"
            class="bg-yellow-500 text-white px-6 py-2 rounded"
            @click="showConfirm=true">
        Forward for Validation
    </button>

    {{-- MODAL --}}
    <div x-show="showConfirm"
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

        <div class="bg-white rounded shadow p-6 w-96">
            <h3 class="text-lg font-bold mb-3">Confirm Forward</h3>

            <p class="text-sm mb-3">Optional: upload validation file (PDF / Image)</p>

            <input type="file"
                   name="validation_file"
                   accept=".pdf,image/*"
                   class="w-full border rounded mb-4">

            <div class="flex justify-end gap-2">
                <button type="button"
                        class="border px-4 py-2 rounded"
                        @click="showConfirm=false">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-yellow-500 text-white px-4 py-2 rounded">
                    Yes, Forward
                </button>
            </div>
        </div>
    </div>

@elseif($user->role->name === 'Super Admin')
    <button type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded">
        Save All Awards
    </button>

@else
    <button type="button"
            disabled
            class="bg-gray-400 text-white px-6 py-2 rounded cursor-not-allowed">
        Editing Disabled
    </button>
@endif

</div>
</form>

{{-- ========================================================= --}}
{{-- 🏅 INDIVIDUAL AWARDS FORM (Gold, Silver, Bronze per sport) --}}
{{-- ========================================================= --}}
<form x-show="awardType==='individual'" 
      method="POST" 
      action="{{ route('results_individual.store') }}" 
      x-data="{ showConfirm: false }"
      enctype="multipart/form-data">

    @csrf
    <input type="hidden" name="division" value="{{ $tab['division'] }}">
    @if(!is_null($tab['category']))
        <input type="hidden" name="category" value="{{ $tab['category'] }}">
    @endif

    @php
        $divisions = \App\Models\Division::orderBy('name')->get();

        if($user->role->name === 'Tournament Manager') {
            $accessibleSportIds = is_string($user->sports_access)
                ? json_decode($user->sports_access, true)
                : $user->sports_access;

            $accessibleSports = \App\Models\Sport::whereIn('id', $accessibleSportIds)->get();

            $accessibleTypeIds = is_string($user->sport_type_access)
                ? json_decode($user->sport_type_access, true)
                : $user->sport_type_access;
        } else {
            $accessibleSports = $sports;
            $accessibleTypeIds = null;
        }

        $athletesJson = $athletes->map(fn($a) => [
            'id' => $a->id,
            'name' => $a->last_name . ', ' . $a->first_name,
            'school_division' => strtoupper(trim($a->school_division)),
        ])->values();
    @endphp

<div class="mb-4 text-sm text-gray-600">
    <strong>Note:</strong> You may award any medal independently. Unfilled medals will be ignored.
</div>

@foreach($accessibleSports as $sport)
<h3 class="font-semibold mt-6 text-lg">{{ strtoupper($sport->name) }}</h3>

<table class="w-full border text-sm mt-2">
<thead class="bg-gray-100">
<tr>
    <th class="border px-2 py-2">Event Type</th>
    <th class="border px-2 py-2 bg-yellow-100">Gold</th>
    <th class="border px-2 py-2 bg-gray-200">Silver</th>
    <th class="border px-2 py-2 bg-orange-200">Bronze</th>
</tr>
</thead>

<tbody>
@foreach($sport->types->whereIn('id', $accessibleTypeIds ?? $sport->types->pluck('id')->toArray()) as $type)
<tr>
<td class="border px-3 py-3 font-medium">{{ $type->type_name }}</td>

@foreach(['Gold','Silver','Bronze'] as $medal)
<td class="border px-3 py-3 align-top">

<div x-data="{
    selectedSDO: '',
    athletes: {{ json_encode($athletesJson) }},
    get filteredAthletes() {
        if (!this.selectedSDO) return [];
        const cleanSDO = this.selectedSDO
            .replace(/^SDO\s*-\s*/i, '')
            .trim()
            .toUpperCase();
        return this.athletes.filter(a => a.school_division === cleanSDO);
    }
}">

<label class="block text-xs font-semibold mb-1">
    {{ $medal }} Division (SDO)
</label>

<select x-model="selectedSDO"
        name="individual_results[{{ $sport->id }}][{{ $type->id }}][{{ $medal }}][sdo]"
        class="w-full border rounded px-2 py-1 text-sm mb-2"
        {{ $user->role->name === 'Tournament Manager' ? 'disabled' : '' }}>
    <option value="">-- Optional --</option>
    @foreach($divisions as $divisionOption)
        <option value="{{ $divisionOption->name }}">{{ $divisionOption->name }}</option>
    @endforeach
</select>

<label class="block text-xs font-semibold mb-1">
    {{ $medal }} Athlete
</label>

<select name="individual_results[{{ $sport->id }}][{{ $type->id }}][{{ $medal }}][athlete]"
        class="w-full border rounded px-2 py-1 text-sm"
        :disabled="!selectedSDO"
        {{ $user->role->name === 'Tournament Manager' ? 'disabled' : '' }}>
    <option value="">-- Optional --</option>

    <template x-for="athlete in filteredAthletes" :key="athlete.id">
        <option :value="athlete.id" x-text="athlete.name"></option>
    </template>
</select>

</div>

</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
@endforeach

{{-- ================= ACTION BUTTONS ================= --}}
<div class="flex justify-end mt-6">

@if($user->role->name === 'Tournament Secretary')
<button type="button"
        class="bg-yellow-500 text-white px-6 py-2 rounded"
        @click="showConfirm=true">
    Forward for Validation
</button>

<div x-show="showConfirm"
     class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
<div class="bg-white rounded shadow p-6 w-96">
    <h3 class="text-lg font-bold mb-3">Confirm Forward</h3>

    <p class="text-sm mb-3">Optional: upload validation file</p>

    <input type="file" name="validation_file"
           accept=".pdf,image/*"
           class="w-full border rounded mb-4">

    <div class="flex justify-end gap-2">
        <button type="button" class="border px-4 py-2 rounded"
                @click="showConfirm=false">Cancel</button>
        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">
            Yes, Forward
        </button>
    </div>
</div>
</div>

@elseif($user->role->name === 'Super Admin')
<button type="submit"
        class="bg-green-600 text-white px-6 py-2 rounded">
    Save Individual Awards
</button>
@endif

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
        individualResults:@json($prefillIndividual),

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