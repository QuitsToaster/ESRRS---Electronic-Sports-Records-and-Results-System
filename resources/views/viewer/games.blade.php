@extends('layouts.viewer')

@section('page-title', 'Games')

@section('content')

{{-- PAGE BACKGROUND --}}
<div class="relative min-h-screen overflow-hidden">

    {{-- Background Image --}}
    <div class="absolute inset-0 -z-10">
        <img src="{{ asset('storage/game1.jpg') }}"
             class="w-full h-full object-cover scale-105 brightness-90 blur-sm"
             alt="Background">

        {{-- DARK + BLUR OVERLAY --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    </div>

    {{-- CONTENT --}}
    <div class="max-w-7xl mx-auto px-4 py-10 space-y-6 relative z-10">

        <h2 class="text-7xl sm:text-7xl font-bold text-white tracking-widest uppercase
           drop-shadow-[0_2px_6px_rgba(0,0,0,0.8)]"
    style="font-family: 'Teko', sans-serif;">
    Game Schedule
</h2>


        {{-- RESPONSIVE GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($games as $game)

                @php
                    // Fetch divisions instead of schools
                    $divisionA = \App\Models\Division::where('name', $game->team_a)->first();
                    $divisionB = \App\Models\Division::where('name', $game->team_b)->first();

                    $status = $game->status ?? 'Upcoming';

                    if($status === 'Upcoming') {
                        $statusClass = 'bg-yellow-100 text-yellow-800';
                    } elseif($status === 'Ongoing') {
                        $statusClass = 'bg-green-100 text-green-800';
                    } else {
                        $statusClass = 'bg-gray-200 text-gray-800';
                    }
                @endphp

                {{-- GAME CARD --}}
                <div onclick="openGame({{ $game->id }})"
                     class="group cursor-pointer bg-white shadow-md overflow-hidden relative
                            transition-all duration-300 ease-out
                            hover:-translate-y-1 hover:shadow-2xl">

                    {{-- STATUS BADGE --}}
                    <span class="absolute top-3 right-3 z-20 px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                        {{ $status }}
                    </span>

                    {{-- GLOSSY HOVER --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent
                                opacity-0 group-hover:opacity-100 transition duration-500 pointer-events-none"></div>

                    {{-- ================= RESPONSIVE MATCH LAYOUT ================= --}}
                    <div class="
                        grid
                        grid-cols-1
                        md:grid-cols-[1fr_1.6fr_1fr]
                        h-auto md:h-40
                    ">

                        {{-- TEAM A --}}
                        <div class="relative overflow-hidden bg-black h-32 md:h-full">
                            @if($divisionA && $divisionA->logo)
                                <div class="absolute inset-0 bg-cover bg-center
                                            transition-transform duration-500 group-hover:scale-110"
                                     style="background-image:url('{{ asset('storage/' . $divisionA->logo) }}')"></div>
                                <div class="absolute inset-0 bg-black/20"></div>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-white text-sm">
                                    {{ $game->team_a }}
                                </div>
                            @endif
                        </div>

                        {{-- CENTER PANEL --}}
                        <div class="bg-black text-white flex flex-col items-center justify-center
                                    text-center px-4 py-4 md:py-0"
                            style="font-family: 'Teko', sans-serif;">

                            {{-- DATE --}}
                            <p class="text-xs sm:text-sm tracking-widest opacity-80 uppercase">
                                {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y') }}
                            </p>

                            {{-- TIME (SCOREBOARD STYLE) --}}
                            <p class="text-4xl sm:text-5xl md:text-6xl font-bold leading-none
                                    tracking-wider drop-shadow-[0_3px_8px_rgba(0,0,0,0.9)]">
                                {{ \Carbon\Carbon::parse($game->game_time)->format('h:i A') }}
                            </p>

                            {{-- DIVIDER --}}
                            <div class="w-12 h-[2px] bg-white my-2 opacity-80"></div>

                            {{-- EVENT NAME --}}
                            <p class="text-[10px] sm:text-xs uppercase tracking-[0.25em] opacity-70">
                                {{ $game->event->name }}
                            </p>
                        </div>

                        {{-- TEAM B --}}
                        <div class="relative overflow-hidden bg-black h-32 md:h-full">
                            @if($divisionB && $divisionB->logo)
                                <div class="absolute inset-0 bg-cover bg-center
                                            transition-transform duration-500 group-hover:scale-110"
                                     style="background-image:url('{{ asset('storage/' . $divisionB->logo) }}')"></div>
                                <div class="absolute inset-0 bg-black/20"></div>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-white text-sm">
                                    {{ $game->team_b }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- TEAM NAMES BAR --}}
                    <div class="grid grid-cols-3 text-center py-2 bg-gray-100"
                        style="font-family: 'Teko', sans-serif;">

                        <span class="truncate px-2 text-sm sm:text-base font-semibold tracking-wide">
                            {{ $game->team_a }}
                        </span>

                        <span class="text-gray-400 text-xs sm:text-sm tracking-widest">
                            VS
                        </span>

                        <span class="truncate px-2 text-sm sm:text-base font-semibold tracking-wide">
                            {{ $game->team_b }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ================= FULL PAGE MODAL ================= --}}
<div id="gameModal"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/70">

    {{-- modal content wrapper, full width --}}
    <div class="relative w-full min-h-screen p-0">
        <button onclick="closeGame()"
                class="absolute top-4 right-4 text-xl font-bold z-50">✕</button>

        <div id="modalContent" class="w-full h-full">
            {{-- loaded dynamically --}}
        </div>
    </div>
</div>

<script>
let liveInterval = null;

function openGame(gameId, persist = true) {
    document.getElementById('gameModal').classList.remove('hidden');

    if (persist) localStorage.setItem('openGameId', gameId);

    fetch(`/viewer/game/${gameId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            startLive(gameId);
        });

    history.pushState({ gameId }, '', `/viewer/game/${gameId}`);
}

function closeGame() {
    document.getElementById('gameModal').classList.add('hidden');
    document.getElementById('modalContent').innerHTML = '';

    localStorage.removeItem('openGameId');

    if (liveInterval) {
        clearInterval(liveInterval);
        liveInterval = null;
    }
}

window.addEventListener('popstate', (event) => {
    if (event.state && event.state.gameId) {
        openGame(event.state.gameId, false);
    } else {
        closeGame();
    }
});

function startLive(gameId) {
    if (liveInterval) clearInterval(liveInterval);

    liveInterval = setInterval(() => {
        fetch(`/viewer/game/${gameId}/live`)
            .then(res => res.json())
            .then(data => {
                if (document.getElementById('liveScoreA')) {
                    document.getElementById('liveScoreA').innerText = data.score_a;
                }
                if (document.getElementById('liveScoreB')) {
                    document.getElementById('liveScoreB').innerText = data.score_b;
                }
            })
            .catch(() => {});
    }, 3000);
}
</script>

@endsection
