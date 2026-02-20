@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\Athlete;

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

// ================= PREFILL ALL AWARDS RESULTS =================
$prefillResults = [];
foreach ($tabs as $tabName => $tab) {
    $query = AllAwardResult::where('division', $tab['division']);
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
        $prefillResults[$tabName]
            [$r->sport_id]
            [$r->type_id ?? 0]
            [$r->sdo]
            [$r->medal] = $r->count;
    }
}

// ================= PREFILL INDIVIDUAL AWARDS =================
$prefillIndividual = [];
foreach ($tabs as $tabName => $tab) {
    $query = IndividualAwardResult::where('division', $tab['division']);
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
        $prefillIndividual[$tabName]
            [$r->sport_id]
            [$r->type_id ?? 0]
            [$r->sdo]
            [$r->medal] = $r->athlete_id;
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

            {{-- ========================================================= --}}
            {{-- 📝 ALL AWARDS FORM --}}
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
                        <thead>
                            <tr class="bg-gray-200 text-center">
                                <th rowspan="2" class="border px-2">Event Type</th>
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

            {{-- ========================================================= --}}
{{-- 🏅 INDIVIDUAL AWARDS FORM (Gold, Silver, Bronze per sport) --}}
{{-- ========================================================= --}}
<form x-show="awardType==='individual'" method="POST" action="{{ route('results_individual.store') }}">
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

    {{-- ================= SELECTION FORM ================= --}}
    @foreach($sports as $sport)
        <h3 class="font-semibold mt-6">{{ strtoupper($sport->name) }}</h3>
        <table class="w-full border text-sm mt-2">
            <tbody>
                @foreach($sport->types as $type)
                    @php $typeKey = $type->id ?? 0; @endphp
                    <tr>
                        <td class="border px-2 font-semibold">{{ $type->type_name }}</td>
                        @foreach(['Gold','Silver','Bronze'] as $medal)
                            <td class="border px-1">
                                <select class="w-full border rounded text-sm"
                                        x-model="individualResults['{{ $tabName }}']['{{ $sport->id }}']['{{ $typeKey }}']['{{ $medal }}']"
                                        name="individual_results[{{ $sport->id }}][{{ $typeKey }}][{{ $medal }}]">
                                    <option value="">-- Select Athlete --</option>
                                    @foreach($tabAthletes as $athlete)
                                        <option value="{{ $athlete->id }}"
                                            {{ (isset($summary[$sport->id][$typeKey][$medal]) && $summary[$sport->id][$typeKey][$medal]->id == $athlete->id) ? 'selected' : '' }}>
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

    <div class="flex justify-end mt-6">
        <button class="bg-green-600 text-white px-6 py-2 rounded">Save Individual Awards</button>
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