@extends('layouts.app')

@section('page-title', 'Manage Scores')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-xl font-semibold text-gray-800">
        Manage Scores: {{ $game->team_a }} vs {{ $game->team_b }}
    </h2>
    <a href="{{ route('games.index') }}"
       class="text-blue-600 hover:text-blue-800 font-semibold">
        Back to Games
    </a>
</div>

@php
    $scores = json_decode($game->scores, true) ?? ['team_a' => [], 'team_b' => []];

    // Determine current period
    $currentPeriodIndex = 0;
    foreach ($periods as $i => $period) {
        if (!isset($scores['team_a'][$i]) && !isset($scores['team_b'][$i])) {
            $currentPeriodIndex = $i;
            break;
        }
        $currentPeriodIndex = $i + 1;
    }

    $currentPeriodIndex = min($currentPeriodIndex, count($periods) - 1);
    $currentPeriod = $periods[$currentPeriodIndex];

    // LIVE SCORE ONLY (resets after each finish)
    $currentScoreA = $game->score_a ?? 0;
    $currentScoreB = $game->score_b ?? 0;
@endphp

{{-- ================= CURRENT PERIOD SCORING ================= --}}
<div class="bg-white border rounded-xl shadow-lg p-6 mb-8">
    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Current Period</h3>
        <p class="text-2xl font-bold text-indigo-600">{{ $currentPeriod }}</p>
    </div>

    <div class="grid grid-cols-3 gap-4 items-center">

        {{-- TEAM A --}}
        <div class="text-center">
            <h4 class="font-semibold mb-3">{{ $game->team_a }}</h4>

            <div class="flex justify-center items-center gap-3">
                <button onclick="changeScore('a', -1)"
                        class="w-10 h-10 rounded-full bg-gray-200 text-xl">−</button>

                <span id="scoreA"
                      class="text-5xl font-bold text-green-600">
                    {{ $currentScoreA }}
                </span>

                <button onclick="changeScore('a', 1)"
                        class="w-10 h-10 rounded-full bg-gray-200 text-xl">+</button>
            </div>
        </div>

        <div class="text-xl font-semibold text-gray-400 text-center">VS</div>

        {{-- TEAM B --}}
        <div class="text-center">
            <h4 class="font-semibold mb-3">{{ $game->team_b }}</h4>

            <div class="flex justify-center items-center gap-3">
                <button onclick="changeScore('b', -1)"
                        class="w-10 h-10 rounded-full bg-gray-200 text-xl">−</button>

                <span id="scoreB"
                      class="text-5xl font-bold text-red-600">
                    {{ $currentScoreB }}
                </span>

                <button onclick="changeScore('b', 1)"
                        class="w-10 h-10 rounded-full bg-gray-200 text-xl">+</button>
            </div>
        </div>
    </div>

    {{-- FINISH PERIOD --}}
    <form method="POST"
          action="{{ route('games.updatePeriodScore', [$game->id, $currentPeriodIndex]) }}"
          class="text-center mt-8"
          onsubmit="finishPeriod()">
        @csrf

        <input type="hidden" name="score_a" id="finalScoreA">
        <input type="hidden" name="score_b" id="finalScoreB">

        <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg text-lg">
            Finish {{ $currentPeriod }}
        </button>
    </form>
</div>

{{-- ================= PERIOD SCORE SUMMARY ================= --}}
<div class="bg-gray-50 border rounded-xl shadow p-6">
    <h3 class="text-lg font-semibold text-gray-700 mb-4 text-center">
        Period Score Summary
    </h3>

    <div class="grid grid-cols-2 gap-6 text-sm">

        {{-- TEAM A --}}
        <div class="text-center">
            <h4 class="font-semibold text-gray-800 mb-2">{{ $game->team_a }}</h4>

            @foreach($periods as $index => $period)
                <div class="flex justify-between px-4 py-1 border-b">
                    <span class="text-gray-600">{{ $period }}</span>
                    <span class="font-semibold">
                        {{ $scores['team_a'][$index] ?? 0 }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- TEAM B --}}
        <div class="text-center">
            <h4 class="font-semibold text-gray-800 mb-2">{{ $game->team_b }}</h4>

            @foreach($periods as $index => $period)
                <div class="flex justify-between px-4 py-1 border-b">
                    <span class="text-gray-600">{{ $period }}</span>
                    <span class="font-semibold">
                        {{ $scores['team_b'][$index] ?? 0 }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ================= LIVE SCORE SCRIPT ================= --}}
<script>
let scoreA = {{ $currentScoreA }};
let scoreB = {{ $currentScoreB }};

function changeScore(team, change) {
    if (team === 'a') {
        scoreA = Math.max(0, scoreA + change);
        document.getElementById('scoreA').innerText = scoreA;
    } else {
        scoreB = Math.max(0, scoreB + change);
        document.getElementById('scoreB').innerText = scoreB;
    }

    fetch("{{ route('games.liveScore', $game->id) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            score_a: scoreA,
            score_b: scoreB
        })
    });
}

function finishPeriod() {
    // send final scores
    document.getElementById('finalScoreA').value = scoreA;
    document.getElementById('finalScoreB').value = scoreB;

    // reset UI immediately (backend should also reset)
    scoreA = 0;
    scoreB = 0;
    document.getElementById('scoreA').innerText = 0;
    document.getElementById('scoreB').innerText = 0;
}
</script>
@endsection
