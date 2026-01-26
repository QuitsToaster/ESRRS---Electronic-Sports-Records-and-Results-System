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

{{-- ================= SET GAME TIMER & SHOT CLOCK ================= --}}
<div class="bg-white border rounded-xl shadow-lg p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Set Game Timer & Shot Clock</h3>

    <div class="flex items-center gap-3">
        <label class="font-semibold">Game Duration (minutes):</label>
        <input type="number" id="gameDurationInput" value="{{ $game->game_duration ?? 48 }}"
               class="w-20 px-3 py-1 border rounded-lg text-center">

        <label class="font-semibold ml-4">Shot Clock (seconds):</label>
        <input type="number" id="shotClockInput" value="{{ $game->shot_clock ?? 24 }}"
               class="w-20 px-3 py-1 border rounded-lg text-center">

        <button onclick="saveGameTimer()" class="px-3 py-1 bg-indigo-600 text-white rounded-lg">Save</button>
    </div>
</div>

{{-- ================= CURRENT PERIOD SCORING WITH TIMERS ================= --}}
<div class="bg-white border rounded-xl shadow-lg p-6 mb-8">
    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Current Period</h3>
        <p class="text-2xl font-bold text-indigo-600">{{ $currentPeriod }}</p>
    </div>

    {{-- TIMER DISPLAY --}}
    <div class="flex justify-center items-center gap-8 mb-4">
        <div>
            <h4 class="font-semibold text-gray-700">Game Timer</h4>
            <span id="gameTimerDisplay" class="text-3xl font-bold text-indigo-600">{{ $game->game_duration ?? 48 }}:00</span>
            <div class="mt-2 flex gap-2 justify-center">
                <button onclick="startGameTimer()" class="px-3 py-1 bg-green-500 text-white rounded-lg">Start</button>
                <button onclick="pauseGameTimer()" class="px-3 py-1 bg-yellow-400 text-white rounded-lg">Pause</button>
                <button onclick="resetGameTimer()" class="px-3 py-1 bg-red-500 text-white rounded-lg">Reset</button>
            </div>
        </div>

        <div>
            <h4 class="font-semibold text-gray-700">Shot Clock</h4>
            <span id="shotClockDisplay" class="text-3xl font-bold text-red-600">{{ $game->shot_clock ?? 24 }}</span>
            <div class="mt-2 flex gap-2 justify-center">
                <button onclick="startShotClock()" class="px-3 py-1 bg-green-500 text-white rounded-lg">Start</button>
                <button onclick="pauseShotClock()" class="px-3 py-1 bg-yellow-400 text-white rounded-lg">Pause</button>
                <button onclick="resetShotClock()" class="px-3 py-1 bg-red-500 text-white rounded-lg">Reset</button>
            </div>
        </div>
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

    {{-- FINISH PERIOD --}}
<form id="finishPeriodForm" method="POST" action="{{ route('games.updatePeriodScore', [$game->id, $currentPeriodIndex]) }}" class="text-center mt-8">
    @csrf
    <input type="hidden" name="score_a" id="finalScoreA">
    <input type="hidden" name="score_b" id="finalScoreB">
    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg text-lg">
        Finish {{ $currentPeriod }}
    </button>
</form>

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

// ✅ Attach finishPeriod to form submit
document.getElementById('finishPeriodForm').addEventListener('submit', function(e) {
    // Set hidden inputs before form submission
    document.getElementById('finalScoreA').value = scoreA;
    document.getElementById('finalScoreB').value = scoreB;

    // Reset live scores UI immediately
    scoreA = 0;
    scoreB = 0;
    document.getElementById('scoreA').innerText = 0;
    document.getElementById('scoreB').innerText = 0;
});
</script>

</div>

<script>
// ====== SAVE GAME TIMER & SHOT CLOCK ======
function saveGameTimer() {
    let duration = document.getElementById('gameDurationInput').value;
    let shot = document.getElementById('shotClockInput').value;

    fetch("{{ route('games.updateTimer', $game->id) }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json", 
            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
        },
        body: JSON.stringify({ game_duration: duration, shot_clock: shot })
    }).then(res => {
        alert('Game timer and shot clock saved!');
        gameDuration = duration * 60;
        shotClock = shot;
        document.getElementById('gameTimerDisplay').innerText = duration + ":00";
        document.getElementById('shotClockDisplay').innerText = shotClock;
    });
}

// ====== GAME TIMER ======
let gameDuration = {{ $game->game_duration ?? 48 }} * 60;
let gameTimerInterval = null;

function startGameTimer() {
    if (gameTimerInterval) return;
    gameTimerInterval = setInterval(() => {
        if (gameDuration > 0) {
            gameDuration--;
            let min = Math.floor(gameDuration / 60);
            let sec = gameDuration % 60;
            document.getElementById('gameTimerDisplay').innerText = `${min}:${sec.toString().padStart(2,'0')}`;
        } else {
            clearInterval(gameTimerInterval);
            gameTimerInterval = null;
        }
    }, 1000);
}

function pauseGameTimer() {
    clearInterval(gameTimerInterval);
    gameTimerInterval = null;
}

function resetGameTimer() {
    gameDuration = {{ $game->game_duration ?? 48 }} * 60;
    let min = Math.floor(gameDuration / 60);
    let sec = gameDuration % 60;
    document.getElementById('gameTimerDisplay').innerText = `${min}:${sec.toString().padStart(2,'0')}`;
    pauseGameTimer();
}

// ====== SHOT CLOCK ======
let shotClock = {{ $game->shot_clock ?? 24 }};
let shotClockInterval = null;

function startShotClock() {
    if (shotClockInterval) return;
    shotClockInterval = setInterval(() => {
        if (shotClock > 0) {
            shotClock--;
            document.getElementById('shotClockDisplay').innerText = shotClock;
        } else {
            clearInterval(shotClockInterval);
            shotClockInterval = null;
        }
    }, 1000);
}

function pauseShotClock() {
    clearInterval(shotClockInterval);
    shotClockInterval = null;
}

function resetShotClock() {
    shotClock = {{ $game->shot_clock ?? 24 }};
    document.getElementById('shotClockDisplay').innerText = shotClock;
    pauseShotClock();
}
</script>


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
