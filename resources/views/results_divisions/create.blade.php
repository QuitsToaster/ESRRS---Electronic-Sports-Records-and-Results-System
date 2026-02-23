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
$athletes = \App\Models\Athlete::orderBy('last_name')->get();

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

{{-- ================= PENDING AWARDS FOR TOURNAMENT MANAGER / SUPER ADMIN ================= --}}
@if($user->role->name === 'Tournament Manager' || $user->role->name === 'Super Admin')
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

                        {{-- ✅ CLICKABLE VALIDATION FILE --}}
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
                                @click="$dispatch('open-modal', { awardId: {{ $r->id }}, type: 'all' })">
                                Approve
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

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

                        {{-- ✅ CLICKABLE VALIDATION FILE --}}
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
@endif


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
        $sportsQuery = \App\Models\Sport::with('types')->where('division', $tab['division'])->orderBy('name');
        if ($user->role->name !== 'Super Admin') {
            $sportsQuery->whereIn('id', $user->sports_access);
        }
        $sports = $sportsQuery->get();

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
            </div>

          {{-- ================================================= --}}
{{-- 🏆 ALL AWARDS FORM --}}
{{-- ================================================= --}}
<form method="POST"
      action="{{ route('results_all_awards.store') }}"
      enctype="multipart/form-data"
      x-show="awardType==='all'"
      x-data="{ showConfirm:false }">
@csrf

<input type="hidden" name="division" value="{{ $tab['division'] }}">
<input type="hidden" name="category" value="{{ $tab['category'] ?? '' }}">

@foreach($sports as $sport)
    <h3 class="font-semibold mt-6">
        {{ strtoupper($sport->name) }}
        @if(strtolower($sport->division) === 'paragames')
            — {{ $sport->category ?? 'N/A' }}
        @endif
    </h3>

<table class="w-full border text-sm mt-2">
<thead class="bg-gray-200 text-center">
<tr>
    <th class="border">Event Type</th>
    @foreach($divisions as $d)
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
@foreach($divisions as $d)
@foreach(['Gold','Silver','Bronze'] as $medal)
<td class="border text-center">
<input type="number" min="0" value="0"
    class="w-16 border rounded text-center"
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
@if($user->role->name === 'Tournament Secretary')
<button type="button"
    class="bg-yellow-500 text-white px-6 py-2 rounded"
    @click="showConfirm=true">
    Forward for Validation
</button>

{{-- MODAL --}}
<div x-show="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
<div class="bg-white rounded shadow p-6 w-96">
<h3 class="text-lg font-bold mb-3">Confirm Forward</h3>

<p class="text-sm mb-3">Optional: upload validation file (PDF / Image)</p>

<input type="file"
       name="validation_file"
       accept=".pdf,image/*"
       class="w-full border rounded mb-4">

<div class="flex justify-end gap-2">
<button type="button" class="border px-4 py-2 rounded" @click="showConfirm=false">Cancel</button>
<button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Yes, Forward</button>
</div>
</div>
</div>
@else
<button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">
Save All Awards
</button>
@endif
</div>
</form>


            {{-- ========================================================= --}}
{{-- 🏅 INDIVIDUAL AWARDS FORM (Gold, Silver, Bronze per sport) --}}
{{-- ========================================================= --}}
<form x-show="awardType==='individual'" method="POST" action="{{ route('results_individual.store') }}" x-data="{ showConfirm: false }">
    @csrf
    <input type="hidden" name="division" value="{{ $tab['division'] }}">
    @if(!is_null($tab['category']))
        <input type="hidden" name="category" value="{{ $tab['category'] }}">
    @endif

    @php
        // Prepare summary from database
        $summary = [];
        $eventId = \App\Models\Event::where('is_active', 1)
            ->where('age_group', $tab['division'])
            ->value('id');

        if ($eventId) {
            $awardResults = \App\Models\IndividualAwardResult::where('event_id', $eventId)
                ->where('division', $tab['division'])
                ->where('category', $tab['category'] ?? null)
                ->get();

            foreach ($awardResults as $award) {
                $athlete = \App\Models\Athlete::find($award->athlete_id);
                if ($athlete) {
                    $summary[$award->sport_id][$award->type_id][$award->medal] = $athlete;
                }
            }
        }

        // Filter athletes per tab for the select options
        $tabAthletes = $athletes->filter(fn($a) => $a->division == $tab['division'] && ($tab['category'] ? $a->category == $tab['category'] : true));
    @endphp

    {{-- ================================================= --}}
{{-- 🏅 INDIVIDUAL AWARDS FORM --}}
{{-- ================================================= --}}
<form method="POST"
      action="{{ route('results_individual.store') }}"
      enctype="multipart/form-data"
      x-show="awardType==='individual'"
      x-data="{ showConfirm:false }">
@csrf

<input type="hidden" name="division" value="{{ $tab['division'] }}">
<input type="hidden" name="category" value="{{ $tab['category'] ?? '' }}">

{{-- ================= SUMMARY TABLE ================= --}}
    <div class="overflow-x-auto mb-6">
        <h3 class="text-lg font-bold mb-2">Individual Awards Summary</h3>
        <table class="w-full border text-sm">
            <thead class="bg-gray-200 text-center">
                <tr>
                    <th class="border px-2">Sport</th>
                    <th class="border px-2">Event Type</th>
                    <th class="border px-2">Gold</th>
                    <th class="border px-2">Silver</th>
                    <th class="border px-2">Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sports as $sport)
                    @foreach($sport->types as $type)
                        @php $typeKey = $type->id ?? 0; @endphp
                        <tr>
                            <td class="border px-2 font-semibold">{{ strtoupper($sport->name) }}</td>
                            <td class="border px-2 font-semibold">{{ $type->type_name }}</td>
                            @foreach(['Gold','Silver','Bronze'] as $medal)
                                <td class="border px-2 text-left align-top">
                                    @if(isset($summary[$sport->id][$typeKey][$medal]))
                                        @php $ath = $summary[$sport->id][$typeKey][$medal]; @endphp
                                        <strong>{{ $ath->last_name }}, {{ $ath->first_name }}</strong><br>
                                        <small>Region: {{ $ath->region }}</small><br>
                                        <small>School: {{ $ath->school_name }}</small><br>
                                        <small>Birthdate: {{ $ath->birthdate }}</small>
                                    @else
                                        --
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

@foreach($sports as $sport)
<h3 class="font-semibold mt-6">{{ strtoupper($sport->name) }}</h3>
<table class="w-full border text-sm mt-2">
<tbody>
@foreach($sport->types as $type)
<tr>
<td class="border px-2">{{ $type->type_name }}</td>
@foreach(['Gold','Silver','Bronze'] as $medal)
<td class="border px-1">
<select class="w-full border rounded text-sm"
    name="individual_results[{{ $sport->id }}][{{ $type->id }}][{{ $medal }}]">
<option value="">-- Select Athlete --</option>
@foreach($athletes as $a)
<option value="{{ $a->id }}">{{ $a->last_name }}, {{ $a->first_name }}</option>
@endforeach
</select>
</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
@endforeach

<div class="flex justify-end mt-6">
@if($user->role->name === 'Tournament Secretary')
<button type="button"
    class="bg-yellow-500 text-white px-6 py-2 rounded"
    @click="showConfirm=true">
    Forward for Validation
</button>

<div x-show="showConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
<div class="bg-white rounded shadow p-6 w-96">
<h3 class="text-lg font-bold mb-3">Confirm Forward</h3>

<p class="text-sm mb-3">Optional: upload validation file (PDF / Image)</p>

<input type="file"
       name="validation_file"
       accept=".pdf,image/*"
       class="w-full border rounded mb-4">

<div class="flex justify-end gap-2">
<button type="button" class="border px-4 py-2 rounded" @click="showConfirm=false">Cancel</button>
<button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Yes, Forward</button>
</div>
</div>
</div>
@else
<button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">
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