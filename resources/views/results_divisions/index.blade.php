@extends('layouts.app')

@section('page-title', 'Official Results')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12 gap-6">
    <div class="text-center md:text-left">
        <h1 class="text-5xl font-extrabold text-gray-900 tracking-wide">
            OFFICIAL RESULT
        </h1>
        <p class="text-gray-500 mt-2">
            Medal Tally & Division Summary
        </p>
    </div>

    {{-- ADD RESULT + EXPORT BUTTONS --}}
    <div class="text-center md:text-right flex flex-col md:flex-row gap-2 md:gap-4">
        {{-- Add Result (unchanged / visible to all who already had access) --}}
        @if(in_array(auth()->user()->role->name, ['Super Admin']))
        <a href="{{ route('results_divisions.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow hover:bg-green-700 transition">
            ➕ Add Result
        </a>
        @endif

        {{-- Export PDF (ONLY Tournament Secretary & Super Admin) --}}
        @if(in_array(auth()->user()->role->name, ['Super Admin']))
            <a href="{{ route('results_divisions.export') }}" target="_blank"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow hover:bg-blue-700 transition">
                📄 Export PDF
            </a>
        @endif
    </div>
</div>


@php
use App\Models\Division;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;

// ===========================
// Get all divisions
// ===========================
$allDivisions = Division::orderBy('name')->get();

// ===========================
// Preload ALL APPROVED awards for medal tables
// ===========================
$allAwards = AllAwardResult::where('status', 'approved')
    ->get()
    ->groupBy(function($item) {
        return $item->division . '|' .
               ($item->category ?? 'none') . '|' .
               ($item->sdo ?? 'none') . '|' .
               $item->medal;
    });

// ===========================
// Function to get medal count
// ===========================
function medalCountFromPreloaded($allAwards, $division, $category, $sdo, $medal) {
    $key = $division . '|' . ($category ?? 'none') . '|' . ($sdo ?? 'none') . '|' . $medal;
    return isset($allAwards[$key])
        ? $allAwards[$key]->sum('count')
        : 0;
}

// ===========================
// MAP OVERALL SUMMARY
// ===========================
$overall = $allDivisions->map(function ($division) use ($allAwards) {

    $division->total_gold =
        medalCountFromPreloaded($allAwards, 'Elementary', 'Boys', $division->name, 'Gold') +
        medalCountFromPreloaded($allAwards, 'Elementary', 'Girls', $division->name, 'Gold') +
        medalCountFromPreloaded($allAwards, 'Secondary', 'Boys', $division->name, 'Gold') +
        medalCountFromPreloaded($allAwards, 'Secondary', 'Girls', $division->name, 'Gold');

    $division->total_silver =
        medalCountFromPreloaded($allAwards, 'Elementary', 'Boys', $division->name, 'Silver') +
        medalCountFromPreloaded($allAwards, 'Elementary', 'Girls', $division->name, 'Silver') +
        medalCountFromPreloaded($allAwards, 'Secondary', 'Boys', $division->name, 'Silver') +
        medalCountFromPreloaded($allAwards, 'Secondary', 'Girls', $division->name, 'Silver');

    $division->total_bronze =
        medalCountFromPreloaded($allAwards, 'Elementary', 'Boys', $division->name, 'Bronze') +
        medalCountFromPreloaded($allAwards, 'Elementary', 'Girls', $division->name, 'Bronze') +
        medalCountFromPreloaded($allAwards, 'Secondary', 'Boys', $division->name, 'Bronze') +
        medalCountFromPreloaded($allAwards, 'Secondary', 'Girls', $division->name, 'Bronze');

    $division->total_points =
        ($division->total_gold * 3) +
        ($division->total_silver * 2) +
        ($division->total_bronze * 1);

    return $division;

})->sort(function($a, $b) {
    if ($a->total_gold !== $b->total_gold) return $b->total_gold <=> $a->total_gold;
    if ($a->total_silver !== $b->total_silver) return $b->total_silver <=> $a->total_silver;
    return $b->total_bronze <=> $a->total_bronze;
})->values();

// ===========================
// INDIVIDUAL AWARDS
// ===========================
$individualAwards = IndividualAwardResult::with('athlete','sport','event')
    ->where('status', 'approved')
    ->orderBy('event_id')
    ->get();

$awardsByMedal = $individualAwards->groupBy('medal');

// ===========================
// PARA GAMES MEDAL TABLE
// ===========================
$allAwardsPara = AllAwardResult::where('status','approved')
                ->where('division','Paragames')
                ->get();
@endphp


{{-- ================= OVERALL SUMMARY WITH LOGO ================= --}}
<div class="mb-20">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 border-l-8 border-green-600 pl-4">
        Over-All Summary
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($overall as $index => $division)
            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl shadow-lg p-6 border border-green-200">

                <div class="flex items-center gap-4 mb-4">
                    @if($division->logo)
                        <img src="{{ asset('storage/'.$division->logo) }}"
                             class="w-16 h-16 rounded-full object-cover shadow">
                    @else
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg">
                            {{ strtoupper(substr($division->name,0,2)) }}
                        </div>
                    @endif

                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $division->name }}</h3>
                        <p class="text-sm text-gray-500">Overall Rank #{{ $index + 1 }}</p>
                    </div>
                </div>

                <div class="flex justify-between text-center mt-4">
                    <div>
                        <div class="w-6 h-6 bg-yellow-400 rounded-full mx-auto mb-1"></div>
                        <p class="font-bold text-lg">{{ $division->total_gold }}</p>
                        <p class="text-xs text-gray-500">Gold</p>
                    </div>
                    <div>
                        <div class="w-6 h-6 bg-gray-400 rounded-full mx-auto mb-1"></div>
                        <p class="font-bold text-lg">{{ $division->total_silver }}</p>
                        <p class="text-xs text-gray-500">Silver</p>
                    </div>
                    <div>
                        <div class="w-6 h-6 bg-orange-500 rounded-full mx-auto mb-1"></div>
                        <p class="font-bold text-lg">{{ $division->total_bronze }}</p>
                        <p class="text-xs text-gray-500">Bronze</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ================= INDIVIDUAL AWARDS ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-purple-600 pl-4">
        Individual Awards
    </h2>

    @foreach(['Gold', 'Silver', 'Bronze'] as $medal)
        @if(isset($awardsByMedal[$medal]) && $awardsByMedal[$medal]->count() > 0)
            <div class="mb-10">
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">
                    {{ $medal }} Medalists
                </h3>

                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-purple-700 text-white uppercase text-sm">
                            <tr>
                                <th class="px-4 py-3">Event</th>
                                <th class="px-4 py-3">Event Type</th>
                                <th class="px-4 py-3">Athlete Name</th>
                                <th class="px-4 py-3">School</th>
                                <th class="px-4 py-3">Region</th>
                                <th class="px-4 py-3">Birthdate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($awardsByMedal[$medal] as $award)
                                @php
                                    $athlete = $award->athlete;
                                    $sport   = $award->sport;
                                    $event   = $award->event;
                                @endphp
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $event ? $event->name : 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $sport ? $sport->name ?? 'N/A' : 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $athlete ? $athlete->last_name . ', ' . $athlete->first_name : 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $athlete ? $athlete->school_name : 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $athlete ? $athlete->region : 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $athlete ? $athlete->birthdate : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
</div>


{{-- ================= ELEMENTARY ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-blue-600 pl-4">
        Elementary Division Medal Summary
    </h2>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead>
                <tr class="bg-gray-900 text-white uppercase text-sm">
                    <th rowspan="2" class="px-4 py-3 text-left">SDO</th>
                    <th colspan="3" class="bg-blue-700">Boys</th>
                    <th colspan="3" class="bg-pink-600">Girls</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700">
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDivisions as $division) {{-- SDO list --}}
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-left font-semibold">{{ $division->name }}</td>

                        {{-- Boys --}}
                        <td class="font-bold text-yellow-500">{{ medalCountFromPreloaded($allAwards, 'Elementary', 'Boys', $division->name, 'Gold') }}</td>
                        <td class="font-bold text-gray-500">{{ medalCountFromPreloaded($allAwards, 'Elementary', 'Boys', $division->name, 'Silver') }}</td>
                        <td class="font-bold text-orange-500">{{ medalCountFromPreloaded($allAwards, 'Elementary', 'Boys', $division->name, 'Bronze') }}</td>

                        {{-- Girls --}}
                        <td class="font-bold text-yellow-500">{{ medalCountFromPreloaded($allAwards, 'Elementary', 'Girls', $division->name, 'Gold') }}</td>
                        <td class="font-bold text-gray-500">{{ medalCountFromPreloaded($allAwards, 'Elementary', 'Girls', $division->name, 'Silver') }}</td>
                        <td class="font-bold text-orange-500">{{ medalCountFromPreloaded($allAwards, 'Elementary', 'Girls', $division->name, 'Bronze') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ================= SECONDARY ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-blue-600 pl-4">
        Secondary Division Medal Summary
    </h2>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead>
                <tr class="bg-gray-900 text-white uppercase text-sm">
                    <th rowspan="2" class="px-4 py-3 text-left">SDO</th>
                    <th colspan="3" class="bg-blue-700">Boys</th>
                    <th colspan="3" class="bg-pink-600">Girls</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700">
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDivisions as $division)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-left font-semibold">{{ $division->name }}</td>

                        {{-- Boys --}}
                        <td class="font-bold text-yellow-500">{{ medalCountFromPreloaded($allAwards, 'Secondary', 'Boys', $division->name, 'Gold') }}</td>
                        <td class="font-bold text-gray-500">{{ medalCountFromPreloaded($allAwards, 'Secondary', 'Boys', $division->name, 'Silver') }}</td>
                        <td class="font-bold text-orange-500">{{ medalCountFromPreloaded($allAwards, 'Secondary', 'Boys', $division->name, 'Bronze') }}</td>

                        {{-- Girls --}}
                        <td class="font-bold text-yellow-500">{{ medalCountFromPreloaded($allAwards, 'Secondary', 'Girls', $division->name, 'Gold') }}</td>
                        <td class="font-bold text-gray-500">{{ medalCountFromPreloaded($allAwards, 'Secondary', 'Girls', $division->name, 'Silver') }}</td>
                        <td class="font-bold text-orange-500">{{ medalCountFromPreloaded($allAwards, 'Secondary', 'Girls', $division->name, 'Bronze') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ================= PARA GAMES MEDAL SUMMARY ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-blue-600 pl-4">
        Para Games Division Medal Summary
    </h2>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead class="bg-purple-700 text-white uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">SDO</th>
                    <th>Gold</th>
                    <th>Silver</th>
                    <th>Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDivisions as $division)
                    @php
                        $gold   = $allAwardsPara->where('sdo', $division->name)->where('medal','Gold')->sum('count');
                        $silver = $allAwardsPara->where('sdo', $division->name)->where('medal','Silver')->sum('count');
                        $bronze = $allAwardsPara->where('sdo', $division->name)->where('medal','Bronze')->sum('count');
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-left font-semibold">{{ $division->name }}</td>
                        <td class="font-bold text-yellow-500">{{ $gold }}</td>
                        <td class="font-bold text-gray-500">{{ $silver }}</td>
                        <td class="font-bold text-orange-500">{{ $bronze }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
