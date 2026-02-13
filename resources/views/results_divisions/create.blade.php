@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')

@php
    use App\Models\Sport;
    use App\Models\Athlete;

    $tabs = [
        'Elementary Boys' => ['level' => 'Elementary', 'category' => 'Boys'],
        'Elementary Girls' => ['level' => 'Elementary', 'category' => 'Girls'],
        'Secondary Boys' => ['level' => 'Secondary', 'category' => 'Boys'],
        'Secondary Girls' => ['level' => 'Secondary', 'category' => 'Girls'],
        'Para Games' => ['level' => 'Paragames', 'category' => null], // ✅ FIXED
    ];
@endphp


{{-- ================= TOP BACK BUTTON ================= --}}
<div class="mb-4">
    <a href="{{ route('results_divisions.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded transition">
        ← Back to Results
    </a>
</div>


{{-- ================= TABS ================= --}}
<div class="mb-6">
    <ul class="flex border-b border-gray-300" id="awardTabs">
        @foreach($tabs as $tabName => $tab)
            <li class="mr-2">
                <a href="#{{ Str::slug($tabName) }}" 
                   class="tab-link inline-block py-2 px-4 font-semibold text-gray-600 
                          border-b-2 border-transparent 
                          hover:text-blue-600 hover:border-blue-400 transition"
                   onclick="openTab(event, '{{ Str::slug($tabName) }}')">
                    {{ $tabName }}
                </a>
            </li>
        @endforeach
    </ul>
</div>


<form action="{{ route('results_divisions.store') }}" method="POST">
@csrf

@foreach($tabs as $tabName => $tab)
@php
    $level    = $tab['level'];
    $category = $tab['category'];

    /*
    |--------------------------------------------------------------------------
    | SPORTS
    |--------------------------------------------------------------------------
    | Para Games tab  -> ONLY sports with division = Paragames
    | Other tabs      -> ONLY sports NOT Paragames
    */
    $sports = Sport::with('types')
        ->where(function ($q) use ($level) {
            if ($level === 'Paragames') {
                $q->where('division', 'Paragames');
            } else {
                $q->where('division', '!=', 'Paragames');
            }
        })
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | ATHLETES
    |--------------------------------------------------------------------------
    */
    $athletes = Athlete::with('school')
        ->where(function ($q) use ($level, $category) {
            if ($level === 'Paragames') {
                $q->where('division', 'Paragames');
            } else {
                $q->where('division', $level)
                  ->where('category', $category);
            }
        })
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();
@endphp



<div id="{{ Str::slug($tabName) }}" class="tab-content hidden">
<h2 class="text-2xl font-bold mb-4">{{ $tabName }} Awards</h2>

<div class="overflow-x-auto">
<table class="w-full border-collapse text-sm">
<thead class="bg-gray-100">
<tr>
<th class="px-4 py-2 text-left">Sport / Event</th>
<th class="px-4 py-2">Gold</th>
<th class="px-4 py-2">Silver</th>
<th class="px-4 py-2">Bronze</th>
</tr>
</thead>
<tbody>

@foreach($sports as $sport)
<tr class="bg-gray-50 font-bold">
<td colspan="4">{{ strtoupper($sport->name) }}</td>
</tr>

@foreach($sport->types as $type)
<tr class="border-t">
<td class="italic px-4 py-2 text-gray-600">{{ $type->type_name }}</td>

@foreach(['Gold','Silver','Bronze'] as $medal)
<td class="px-2 py-2">

<input type="text"
       placeholder="Type athlete name..."
       class="w-full border rounded px-2 py-1 mb-1 athlete-input text-sm"
       data-target="select">

<select name="results[{{ $level }}][{{ $category ?? 'NA' }}][{{ $sport->id }}][{{ $type->id }}][{{ $medal }}]"
        class="w-full border rounded px-2 py-1 athlete-select text-sm">

<option value="">-- Select Athlete --</option>

@foreach($athletes as $athlete)
<option value="{{ $athlete->id }}"
data-name="{{ strtolower($athlete->first_name . ' ' . $athlete->last_name) }}"
data-info='{{ json_encode([
"school" => $athlete->school->name ?? "-",
"age" => $athlete->birthdate ? \Carbon\Carbon::parse($athlete->birthdate)->age : "-",
"region" => $athlete->region ?? "-"
]) }}'>
{{ $athlete->first_name }} {{ $athlete->last_name }}
</option>
@endforeach

</select>

<div class="mt-1 text-xs text-gray-500 athlete-details"></div>

</td>
@endforeach

</tr>
@endforeach
@endforeach

</tbody>
</table>
</div>
</div>
@endforeach


<div class="flex justify-end mt-6 gap-2">
    <a href="{{ route('results_divisions.index') }}"
       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded transition">
        Cancel
    </a>
    <button type="submit"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
        Save Results
    </button>
</div>

</form>


{{-- ================= TAB + SEARCH JS ================= --}}
<script>
function openTab(evt, tabId) {
    evt.preventDefault();

    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });

    // Show selected tab
    document.getElementById(tabId).classList.remove('hidden');

    // Remove active styles
    document.querySelectorAll('.tab-link').forEach(link => {
        link.classList.remove('text-blue-600','border-blue-600','bg-blue-50');
        link.classList.add('text-gray-600');
    });

    // Add active styles
    evt.currentTarget.classList.remove('text-gray-600');
    evt.currentTarget.classList.add('text-blue-600','border-blue-600','bg-blue-50');
}

document.addEventListener('DOMContentLoaded', () => {

    // Activate first tab by default
    const firstTab = document.querySelectorAll('.tab-content')[0];
    const firstLink = document.querySelectorAll('.tab-link')[0];

    if(firstTab && firstLink){
        firstTab.classList.remove('hidden');
        firstLink.classList.add('text-blue-600','border-blue-600','bg-blue-50');
        firstLink.classList.remove('text-gray-600');
    }

    // Dropdown change = show details
    document.querySelectorAll('.athlete-select').forEach(select => {
        select.addEventListener('change', function() {
            showDetails(this);
        });
    });

    // Typing = auto match dropdown
    document.querySelectorAll('.athlete-input').forEach(input => {
        input.addEventListener('keyup', function() {

            const value = this.value.toLowerCase();
            const select = this.parentElement.querySelector('.athlete-select');
            const options = select.querySelectorAll('option');

            let matched = false;

            options.forEach(option => {
                if(option.dataset.name && option.dataset.name.includes(value)){
                    select.value = option.value;
                    matched = true;
                }
            });

            if(matched){
                showDetails(select);
            }
        });
    });

    function showDetails(select){
        const infoBox = select.parentElement.querySelector('.athlete-details');
        const selected = select.selectedOptions[0];

        if(selected && selected.dataset.info){
            const info = JSON.parse(selected.dataset.info);
            infoBox.innerHTML = `
                School: ${info.school || '-'}<br>
                Age: ${info.age || '-'}<br>
                Region: ${info.region || '-'}
            `;
        } else {
            infoBox.innerHTML = '';
        }
    }

});
</script>

@endsection
