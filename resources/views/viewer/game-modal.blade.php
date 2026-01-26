@extends('layouts.viewer')

@section('page-title', 'Live Score Viewer')

@section('content')

@php
use Carbon\Carbon;

$scores = json_decode($game->scores, true) ?? ['team_a'=>[], 'team_b'=>[]];

$now = Carbon::now();
$gameStart = Carbon::parse($game->game_time);
$gameEnd = $gameStart->copy()->addMinutes($game->game_duration ?? 120); // default 2 hours

if ($now->lt($gameStart)) {
    $status = 'Upcoming';
    $statusColor = 'bg-yellow-400';
} elseif ($now->between($gameStart, $gameEnd)) {
    $status = 'Ongoing';
    $statusColor = 'bg-green-500';
} else {
    $status = 'Finished';
    $statusColor = 'bg-gray-400';
}
@endphp

<div class="max-w-7xl mx-auto px-4 py-8 space-y-8">

    {{-- BACK BUTTON --}}
    <div class="mb-4">
        <a href="{{ route('viewer.games') }}"
           class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow">
            ← Back to Games
        </a>
    </div>
{{-- ================= SCOREBOARD HEADER ================= --}}
<div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white rounded-xl p-6 shadow-lg">

    

    <div class="flex items-center justify-between">

        {{-- TEAM A --}}
        <div class="flex flex-col items-center w-1/3">
            <span class="text-sm uppercase tracking-wide text-gray-300">{{ $game->team_a }}</span>
            <span id="liveScoreA" class="text-6xl md:text-7xl font-extrabold text-green-400">
                {{ $game->score_a ?? 0 }}
            </span>
        </div>

        {{-- VS / STATUS --}}
        <div class="flex flex-col items-center">
            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor ?? 'bg-gray-500' }} mb-1">
                {{ $status ?? $game->status }}
            </span>

            @if(($status ?? $game->status) === 'Ongoing')
                <span class="text-xs text-red-400 font-semibold tracking-widest mb-1 animate-pulse">
                    ● LIVE
                </span>
            @endif

            <span class="text-gray-300 text-sm">{{ $game->event->name }}</span>
        </div>

        {{-- TEAM B --}}
        <div class="flex flex-col items-center w-1/3">
            <span class="text-sm uppercase tracking-wide text-gray-300">{{ $game->team_b }}</span>
            <span id="liveScoreB" class="text-6xl md:text-7xl font-extrabold text-red-400">
                {{ $game->score_b ?? 0 }}
            </span>
        </div>

    </div>
</div>

<script>
// ------------------- VARIABLES -------------------
let gameTimer = {{ $game->game_duration ?? 48 }} * 60;
let shotClock = {{ $game->shot_clock ?? 24 }};
let timerInterval = null;
let shotClockInterval = null;

const gameTimerDisplay = document.getElementById('gameTimerDisplay');
const shotClockDisplay = document.getElementById('shotClockDisplay');

// ------------------- DISPLAY FUNCTION -------------------
function updateDisplays() {
    let min = Math.floor(gameTimer / 60);
    let sec = gameTimer % 60;
    gameTimerDisplay.innerText = `${min.toString().padStart(2,'0')}:${sec.toString().padStart(2,'0')}`;
    shotClockDisplay.innerText = shotClock;
}

// ------------------- LIVE POLLING -------------------
setInterval(async () => {
    try {
        const res = await fetch("{{ url('/viewer/game/'.$game->id.'/live-timer') }}");
        const data = await res.json();

        // Only update if timer is not running locally yet
        if (data.timer_running && !timerInterval) {
            timerInterval = setInterval(() => {
                if (gameTimer > 0) gameTimer--;
                updateDisplays();
            }, 1000);
        }

        if (!data.timer_running && timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        if (data.shot_clock_running && !shotClockInterval) {
            shotClockInterval = setInterval(() => {
                if (shotClock > 0) shotClock--;
                updateDisplays();
            }, 1000);
        }

        if (!data.shot_clock_running && shotClockInterval) {
            clearInterval(shotClockInterval);
            shotClockInterval = null;
        }

        // Sync remaining time from DB every 2–3 seconds to prevent drift
        if (!timerInterval) gameTimer = (data.game_duration ?? 48) * 60;
        if (!shotClockInterval) shotClock = data.shot_clock ?? 24;

        updateDisplays();
    } catch (err) {
        console.error('Error fetching live timer:', err);
    }
}, 2000); // poll every 2 seconds

// ------------------- INITIAL RENDER -------------------
updateDisplays();
</script>



    {{-- ================= LIVE VIDEO ================= --}}
@if($game->live_video_url)
<div class="bg-gray-900 rounded-xl shadow-lg p-4 mb-8">
    <h3 class="text-lg font-semibold text-white mb-2">Live Stream</h3>
    <div class="aspect-w-16 aspect-h-9">
        @php
            $embedUrl = '';
            $url = $game->live_video_url;

            if (Str::contains($url, 'youtube.com') || Str::contains($url, 'youtu.be')) {
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




    {{-- ================= PERIOD / QUARTER SCORES ================= --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="text-lg font-bold mb-4 text-gray-800 text-center">
            Period Breakdown
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="text-sm text-gray-500 border-b">
                        <th class="py-2 text-left">Team</th>
                        @foreach($periods as $p)
                            <th class="py-2">{{ $p }}</th>
                        @endforeach
                        <th class="py-2 font-bold">TOTAL</th>
                    </tr>
                </thead>

                <tbody class="text-sm">
                    {{-- TEAM A --}}
                    <tr class="border-b">
                        <td class="py-3 font-semibold text-left">
                            {{ $game->team_a }}
                        </td>
                        @foreach($periods as $i => $p)
                            <td>{{ $scores['team_a'][$i] ?? 0 }}</td>
                        @endforeach
                        <td class="font-bold">
                            {{ array_sum($scores['team_a'] ?? []) }}
                        </td>
                    </tr>

                    {{-- TEAM B --}}
                    <tr>
                        <td class="py-3 font-semibold text-left">
                            {{ $game->team_b }}
                        </td>
                        @foreach($periods as $i => $p)
                            <td>{{ $scores['team_b'][$i] ?? 0 }}</td>
                        @endforeach
                        <td class="font-bold">
                            {{ array_sum($scores['team_b'] ?? []) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= ROSTERS ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- TEAM A ROSTER --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-800">
                {{ $game->team_a }}
            </h3>

            <ul class="space-y-2 text-sm text-gray-700">
                @foreach($athletesA as $athlete)
                    <li class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span>
                        {{ $athlete->name }}
                    </li>
                @endforeach
            </ul>

            <h4 class="mt-5 font-semibold text-gray-600">Coaches</h4>
            @foreach($coachesA as $coach)
                <p class="text-sm text-gray-700">
                    {{ $coach->name }}
                </p>
            @endforeach
        </div>

        {{-- TEAM B ROSTER --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-800">
                {{ $game->team_b }}
            </h3>

            <ul class="space-y-2 text-sm text-gray-700">
                @foreach($athletesB as $athlete)
                    <li class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        {{ $athlete->name }}
                    </li>
                @endforeach
            </ul>

            <h4 class="mt-5 font-semibold text-gray-600">Coaches</h4>
            @foreach($coachesB as $coach)
                <p class="text-sm text-gray-700">
                    {{ $coach->name }}
                </p>
            @endforeach
        </div>

    </div>
</div>
<script>
function format(sec) {
    const m = Math.floor(sec / 60).toString().padStart(2,'0');
    const s = (sec % 60).toString().padStart(2,'0');
    return `${m}:${s}`;
}

setInterval(async () => {
    const res = await fetch("{{ url('/viewer/game/'.$game->id.'/live-timer') }}");
    const data = await res.json();

    document.getElementById('gameTimerDisplay').innerText =
        format(data.game_time_left);

    document.getElementById('shotClockDisplay').innerText =
        data.shot_clock_left;
}, 1000);
</script>

{{-- ================= LIVE TIMER & SHOT CLOCK SCRIPT ================= --}}
<script>
let gameDuration = {{ $game->game_duration ?? 48 }} * 60; // in seconds
let shotClock = {{ $game->shot_clock ?? 24 }};
let shotClockInterval = null;
let gameTimerInterval = null;

// Initialize displays
function formatTime(sec) {
    const m = Math.floor(sec / 60).toString().padStart(2, '0');
    const s = (sec % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

document.getElementById('gameTimerDisplay').innerText = formatTime(gameDuration);
document.getElementById('shotClockDisplay').innerText = shotClock;

// Start game timer
function startGameTimer() {
    if (gameTimerInterval) return;
    gameTimerInterval = setInterval(() => {
        if (gameDuration > 0) {
            gameDuration--;
            document.getElementById('gameTimerDisplay').innerText = formatTime(gameDuration);
        } else {
            clearInterval(gameTimerInterval);
        }
    }, 1000);
}

// Start shot clock
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

// Pause shot clock
function pauseShotClock() {
    clearInterval(shotClockInterval);
    shotClockInterval = null;
}

// Reset shot clock
function resetShotClock() {
    shotClock = {{ $game->shot_clock ?? 24 }};
    document.getElementById('shotClockDisplay').innerText = shotClock;
    pauseShotClock();
}

// Auto-start both timers if game is ongoing
@if($status === 'Ongoing')
startGameTimer();
startShotClock();
@endif

</script>

@endsection
