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
$scores = $game->scores ?? ['team_a' => [], 'team_b' => []];

// Find current period
$currentPeriodIndex = 0;
foreach ($periods as $i => $p) {
    if (!isset($scores['team_a'][$i])) {
        $currentPeriodIndex = $i;
        break;
    }
}
$currentPeriod = $periods[$currentPeriodIndex];

// LIVE SCORE ONLY
$currentScoreA = $game->score_a ?? 0;
$currentScoreB = $game->score_b ?? 0;
@endphp


{{-- ================= LIVE VIDEO LINK ================= --}}
<div class="bg-white border rounded-xl shadow-lg p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Live Video Link</h3>

    <form id="liveVideoForm" method="POST" action="{{ route('games.updateLiveVideo', $game->id) }}">
        @csrf
        <div class="flex items-center gap-3">
            <input type="url" name="live_video_url" placeholder="Enter Facebook or YouTube URL"
                   value="{{ $game->live_video_url ?? '' }}"
                   class="flex-1 px-3 py-2 border rounded-lg">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                Save
            </button>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            Example: https://www.youtube.com/watch?v=XXXXXXXXXX or https://www.facebook.com/.../live
        </p>
    </form>

    @if($game->live_video_url)
<div class="mt-4">
    <h4 class="font-semibold mb-2">Preview:</h4>
    <div class="aspect-w-16 aspect-h-9">
        @php
            $embedUrl = '';
            $url = $game->live_video_url;

            if (Str::contains($url, 'youtube.com') || Str::contains($url, 'youtu.be')) {
                // Extract YouTube video ID
                if (Str::contains($url, 'v=')) {
                    $videoId = Str::after($url, 'v=');
                    if (Str::contains($videoId, '&')) {
                        $videoId = Str::before($videoId, '&');
                    }
                } elseif (Str::contains($url, 'youtu.be/')) {
                    $videoId = Str::after($url, 'youtu.be/');
                    if (Str::contains($videoId, '?')) {
                        $videoId = Str::before($videoId, '?');
                    }
                }
                $embedUrl = 'https://www.youtube.com/embed/' . $videoId . '?rel=0&modestbranding=1';
            } elseif (Str::contains($url, 'facebook.com')) {
                // Facebook embed URL
                $embedUrl = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($url) . '&show_text=false&width=560';
            }
        @endphp

        @if($embedUrl)
            <iframe class="w-full h-64 md:h-96 rounded-lg"
                    src="{{ $embedUrl }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
            </iframe>
        @endif
    </div>
</div>
@endif


</div>


{{-- ================= CURRENT PERIOD SCORING WITH TIMERS ================= --}}
<div class="bg-white border rounded-xl shadow-lg p-6 mb-8">
    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Current Period</h3>
        <p class="text-2xl font-bold text-indigo-600">{{ $currentPeriod }}</p>
    </div>

    {{-- TEAM SCORES --}}
    <div class="grid grid-cols-3 gap-4 items-center">

        {{-- TEAM A --}}
        <div class="text-center">
            <h4 class="font-semibold mb-3">{{ $game->team_a }}</h4>
            <div class="flex justify-center items-center gap-3">
                <button onclick="changeScore('a', -1)" class="w-10 h-10 rounded-full bg-gray-200 text-xl">−</button>
                <span id="scoreA" class="text-5xl font-bold text-green-600">{{ $currentScoreA }}</span>
                <button onclick="changeScore('a', 1)" class="w-10 h-10 rounded-full bg-gray-200 text-xl">+</button>
            </div>
        </div>

        <div class="text-xl font-semibold text-gray-400 text-center">VS</div>

        {{-- TEAM B --}}
        <div class="text-center">
            <h4 class="font-semibold mb-3">{{ $game->team_b }}</h4>
            <div class="flex justify-center items-center gap-3">
                <button onclick="changeScore('b', -1)" class="w-10 h-10 rounded-full bg-gray-200 text-xl">−</button>
                <span id="scoreB" class="text-5xl font-bold text-red-600">{{ $currentScoreB }}</span>
                <button onclick="changeScore('b', 1)" class="w-10 h-10 rounded-full bg-gray-200 text-xl">+</button>
            </div>
        </div>
    </div>

    {{-- START GAME BUTTON --}}
@if($game->status === 'Upcoming')
<div class="text-center mb-4">
    <form action="{{ route('games.start', $game->id) }}" method="POST">
        @csrf
        <button type="submit"
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg text-lg font-semibold">
            Start Game
        </button>
    </form>
</div>
@endif

{{-- WINNER BUTTONS --}}
@if($game->status === 'Finished' && !$game->winner)
<div class="grid grid-cols-2 gap-4 mb-6 text-center">
    <form action="{{ route('games.winner', $game->id) }}" method="POST">
        @csrf
        <input type="hidden" name="winner" value="{{ $game->team_a }}">
        <button type="submit"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-lg font-semibold">
            {{ $game->team_a }} Wins
        </button>
    </form>

    <form action="{{ route('games.winner', $game->id) }}" method="POST">
        @csrf
        <input type="hidden" name="winner" value="{{ $game->team_b }}">
        <button type="submit"
                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg text-lg font-semibold">
            {{ $game->team_b }} Wins
        </button>
    </form>
</div>
@endif

@if($game->winner)
<div class="text-center mb-6">
    <p class="text-xl font-bold text-green-600">Winner: {{ $game->winner }}</p>
</div>
@endif


    {{-- FINISH PERIOD --}}
<form id="finishPeriodForm" method="POST" action="{{ route('games.updatePeriodScore', [$game->id, $currentPeriodIndex]) }}" class="text-center mt-8">
    @csrf
    <input type="hidden" name="score_a" id="finalScoreA">
    <input type="hidden" name="score_b" id="finalScoreB">
    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg text-lg">
        Finish {{ $currentPeriod }}
    </button>
</form>

{{-- FINISH GAME BUTTON --}}
<div class="text-center mt-6">
    <form action="{{ route('games.finish', $game->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to finish this game?');">
        @csrf
        <button type="submit"
                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg text-lg font-semibold">
            Finish Game
        </button>
    </form>
</div>


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

<script>
let scoreA = {{ $currentScoreA }};
let scoreB = {{ $currentScoreB }};

function changeScore(team, delta) {
    if (team === 'a') {
        scoreA = Math.max(0, scoreA + delta);
        document.getElementById('scoreA').innerText = scoreA;
    } else {
        scoreB = Math.max(0, scoreB + delta);
        document.getElementById('scoreB').innerText = scoreB;
    }

    fetch("{{ route('games.liveScore', $game->id) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ score_a: scoreA, score_b: scoreB })
    });
}

// Finish period
document.getElementById('finishPeriodForm').addEventListener('submit', () => {
    document.getElementById('finalScoreA').value = scoreA;
    document.getElementById('finalScoreB').value = scoreB;

    // RESET UI
    scoreA = 0;
    scoreB = 0;
});
</script>

@endsection