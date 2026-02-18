@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
use App\Models\Sport;
use App\Models\Division;
use App\Models\Athlete;
use App\Models\AllAwardResult;

// Tabs
$tabs = [
    'Elementary Boys'   => ['division' => 'Elementary', 'category' => 'Boys'],
    'Elementary Girls'  => ['division' => 'Elementary', 'category' => 'Girls'],
    'Secondary Boys'    => ['division' => 'Secondary', 'category' => 'Boys'],
    'Secondary Girls'   => ['division' => 'Secondary', 'category' => 'Girls'],
    'Para Games'        => ['division' => 'Paragames', 'category' => null],
];

$divisions = Division::orderBy('name')->get();
$athletes = Athlete::orderBy('last_name')->get();

// Prefill results from all_award_results table grouped by division & category
$prefillResults = [];

foreach ($tabs as $tabName => $tab) {
    $division = $tab['division'];
    $category = $tab['category'];

    $results = AllAwardResult::when($division, fn($q) => $q->where('division', $division))
        ->when($category, fn($q) => $q->where('category', $category))
        ->get();

    foreach ($results as $res) {
        $sportId = $res->sport_id;
        $typeId = $res->type_id ?? 0;
        $sdo = $res->sdo;
        $medal = $res->medal;
        $count = $res->count;

        // Store under tabName to filter by division & category
        $prefillResults[$tabName][$sportId][$typeId][$sdo][$medal] = $count;
    }
}
@endphp

<div x-data="awardApp()" class="p-4 space-y-6">

    {{-- Award Type --}}
    <div class="mb-6">
        <label class="block text-sm font-medium mb-1">Award Type</label>
        <select x-model="awardType" class="border rounded px-3 py-2 w-64">
            <option value="all">All Awards</option>
            <option value="individual">Individual Awards</option>
        </select>
    </div>

    {{-- Tabs --}}
    <ul class="flex border-b mb-6">
        @foreach($tabs as $tabName => $tab)
            <li class="mr-2">
                <button
                    @click.prevent="activeTab='{{ Str::slug($tabName) }}'"
                    :class="activeTab === '{{ Str::slug($tabName) }}'
                        ? 'border-blue-600 text-blue-600'
                        : 'text-gray-600'"
                    class="py-2 px-4 border-b-2 font-semibold"
                >
                    {{ $tabName }}
                </button>
            </li>
        @endforeach
    </ul>

    {{-- Tab Contents --}}
    @foreach($tabs as $tabName => $tab)
        @php
            $division = $tab['division'];
            $category = $tab['category'];

            $sports = Sport::with('types')
                ->where(fn($q) =>
                    $division === 'Paragames'
                        ? $q->where('division','Paragames')
                        : $q->where('division','!=','Paragames')
                )
                ->orderBy('name')
                ->get();
        @endphp

        <div x-show="activeTab === '{{ Str::slug($tabName) }}'" x-cloak>

            <h2 class="text-2xl font-bold mb-4">{{ $tabName }}</h2>

            {{-- ================= ALL AWARDS TABLE (TOP MEDAL COUNT) ================= --}}
            <div x-show="awardType === 'all'" class="space-y-6">
                <div class="overflow-x-auto mb-6">
                    <table class="w-full border text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-200 text-center">
                                <th rowspan="2" class="border px-3">SPORT</th>
                                @foreach($divisions as $div)
                                    <th colspan="3" class="border">{{ $div->name }}</th>
                                @endforeach
                            </tr>
                            <tr class="text-center">
                                @foreach($divisions as $div)
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
                                    @foreach($divisions as $div)
                                        <td class="border text-center" x-text="getTotal('{{ $tabName }}','{{ $sport->id }}', '{{ $div->name }}', 'Gold')">0</td>
                                        <td class="border text-center" x-text="getTotal('{{ $tabName }}','{{ $sport->id }}', '{{ $div->name }}', 'Silver')">0</td>
                                        <td class="border text-center" x-text="getTotal('{{ $tabName }}','{{ $sport->id }}', '{{ $div->name }}', 'Bronze')">0</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ================= FORM TO UPDATE ALL AWARDS ================= --}}
                <form action="{{ route('results_all_awards.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="division" value="{{ $division }}">
                    <input type="hidden" name="category" value="{{ $category }}">

                    @foreach($sports as $sport)
                        <h3 class="font-bold mt-8">{{ strtoupper($sport->name) }}</h3>
                        <table class="w-full border text-sm mt-2">
                            <thead class="bg-gray-200 text-center">
                                <tr>
                                    <th rowspan="2" class="border">Event</th>
                                    @foreach($divisions as $div)
                                        <th colspan="3" class="border">{{ $div->name }}</th>
                                    @endforeach
                                </tr>
                                <tr class="text-center">
                                    @foreach($divisions as $div)
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
                                        @foreach($divisions as $div)
                                            @foreach(['Gold','Silver','Bronze'] as $medal)
                                                <td class="border text-center">
                                                    <input type="number" min="0" class="w-16 text-center border rounded px-1"
                                                        x-model.number="results['{{ $tabName }}']['{{ $sport->id }}'] = results['{{ $tabName }}']['{{ $sport->id }}'] || {}; 
                                                                        results['{{ $tabName }}']['{{ $sport->id }}']['{{ $type->id }}'] = results['{{ $tabName }}']['{{ $sport->id }}']['{{ $type->id }}'] || {}; 
                                                                        results['{{ $tabName }}']['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}'] = results['{{ $tabName }}']['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}'] || {};
                                                                        results['{{ $tabName }}']['{{ $sport->id }}']['{{ $type->id }}']['{{ $div->name }}']['{{ $medal }}']"
                                                        name="results[{{ $sport->id }}][{{ $type->id }}][{{ $div->name }}][{{ $medal }}]"
                                                    >
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

            {{-- ================= INDIVIDUAL AWARDS ================= --}}
            <form x-show="awardType === 'individual'" action="{{ route('results_individual.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="division" value="{{ $division }}">
                <input type="hidden" name="category" value="{{ $category }}">

                @foreach($sports as $sport)
                    <h3 class="font-bold mt-6">{{ strtoupper($sport->name) }}</h3>
                    @foreach($sport->types as $type)
                        <div class="mb-4 border rounded p-4">
                            <p class="font-semibold">{{ $type->type_name }}</p>
                            @foreach(['Gold','Silver','Bronze'] as $medal)
                                <div class="flex items-center space-x-2 mt-2">
                                    <label class="w-24">{{ $medal }}</label>
                                    <select name="individual_results[{{ $sport->id }}][{{ $type->id }}][{{ $medal }}]" class="border rounded px-2 py-1">
                                        <option value="">-- Select Athlete --</option>
                                        @foreach($athletes as $athlete)
                                            <option value="{{ $athlete->id }}">{{ $athlete->last_name }}, {{ $athlete->first_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach

                <div class="flex justify-end mt-6">
                    <button class="bg-green-600 text-white px-6 py-2 rounded">Save Individual Awards</button>
                </div>
            </form>

        </div>
    @endforeach

</div>

{{-- Alpine Initialization --}}
<script>
function awardApp() {
    return {
        activeTab: '{{ Str::slug(array_key_first($tabs)) }}',
        awardType: 'all',
        results: {!! json_encode($prefillResults) !!},

        getTotal(tabName, sportId, sdo, medal) {
            let sum = 0;
            const sport = this.results[tabName]?.[sportId] || {};
            for (let typeId in sport) {
                sum += sport[typeId][sdo]?.[medal] || 0;
            }
            return sum;
        }
    }
}
</script>

@endsection
