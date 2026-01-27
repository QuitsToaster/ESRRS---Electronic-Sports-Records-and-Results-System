@extends('layouts.app')

@section('page-title', 'Add Game')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Add a new game to the system by filling out the form below.
    </p>
</div>

{{-- Tabs --}}
<div class="flex border-b border-gray-300 mb-6">
    <button id="tabButton-single" class="px-4 py-2 font-semibold border-b-2 border-blue-600 text-blue-600" onclick="openTab('single')">Single Game</button>
    <button id="tabButton-bracket" class="px-4 py-2 font-semibold text-gray-600" onclick="openTab('bracket')">Bracket System</button>
</div>

{{-- Tabs Content --}}
<div id="tab-single">
    @include('games.partials.create_single')
</div>

<div id="tab-bracket" class="hidden">
    @include('games.partials.create_bracket')
</div>

<script>
function openTab(tab) {
    // Hide all tabs
    document.getElementById('tab-single').classList.add('hidden');
    document.getElementById('tab-bracket').classList.add('hidden');

    // Remove active styles from buttons
    document.querySelectorAll('.flex > button').forEach(b => {
        b.classList.remove('border-blue-600', 'text-blue-600');
        b.classList.add('text-gray-600');
    });

    // Show selected tab
    document.getElementById('tab-' + tab).classList.remove('hidden');

    // Highlight clicked button
    document.getElementById('tabButton-' + tab).classList.add('border-blue-600', 'text-blue-600');
    document.getElementById('tabButton-' + tab).classList.remove('text-gray-600');
}
</script>
@endsection
