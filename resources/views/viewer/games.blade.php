@extends('layouts.viewer')

@section('page-title', 'Games')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    <h2 class="text-3xl font-bold text-gray-800">Game Schedule</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($games as $game)

            @php
                // Fetch schools
                $schoolA = \App\Models\School::where('name', $game->team_a)->first();
                $schoolB = \App\Models\School::where('name', $game->team_b)->first();

                // ✅ Use status from database
                $status = $game->status ?? 'Upcoming';

                // Set badge classes
                if($status === 'Upcoming') {
                    $statusClass = 'bg-yellow-100 text-yellow-800';
                } elseif($status === 'Ongoing') {
                    $statusClass = 'bg-green-100 text-green-800';
                } else { // Finished
                    $statusClass = 'bg-gray-200 text-gray-800';
                }
            @endphp

            <div onclick="openGame({{ $game->id }})"
                 class="cursor-pointer bg-white rounded-lg shadow hover:shadow-xl transition p-6 relative">

                {{-- STATUS BADGE --}}
                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                    {{ $status }}
                </span>

                {{-- EVENT NAME --}}
                <h3 class="text-lg font-semibold text-indigo-600 mb-4 text-center">
                    {{ $game->event->name }}
                </h3>

                {{-- TEAMS --}}
                <div class="flex justify-between items-center mb-4">

                    {{-- TEAM A --}}
                    <div class="flex flex-col items-center w-1/3 text-center">
                        @if($schoolA && $schoolA->logo)
                            <img src="{{ asset('storage/' . $schoolA->logo) }}"
                                 class="h-16 w-16 object-contain mb-2"
                                 alt="{{ $game->team_a }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-xs mb-2">
                                N/A
                            </div>
                        @endif
                        <span class="font-semibold text-sm">{{ $game->team_a }}</span>
                    </div>

                    {{-- VS --}}
                    <div class="text-gray-400 font-bold text-lg">VS</div>

                    {{-- TEAM B --}}
                    <div class="flex flex-col items-center w-1/3 text-center">
                        @if($schoolB && $schoolB->logo)
                            <img src="{{ asset('storage/' . $schoolB->logo) }}"
                                 class="h-16 w-16 object-contain mb-2"
                                 alt="{{ $game->team_b }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-xs mb-2">
                                N/A
                            </div>
                        @endif
                        <span class="font-semibold text-sm">{{ $game->team_b }}</span>
                    </div>
                </div>

                {{-- DATE --}}
                <p class="text-sm text-gray-500 text-center">
                    {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}
                </p>
            </div>
        @endforeach
    </div>
</div>

{{-- ================= FULL PAGE MODAL ================= --}}
<div id="gameModal"
     class="fixed inset-0 bg-black/70 z-50 hidden overflow-y-auto">

    <div class="bg-white min-h-screen p-6 relative">
        <button onclick="closeGame()"
                class="absolute top-4 right-4 text-xl font-bold">✕</button>

        <div id="modalContent" class="max-w-6xl mx-auto">
            {{-- loaded dynamically --}}
        </div>
    </div>
</div>

<script>
let liveInterval = null;

function openGame(gameId) {
    document.getElementById('gameModal').classList.remove('hidden');

    fetch(`/viewer/game/${gameId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            startLive(gameId);
        });
}

function closeGame() {
    document.getElementById('gameModal').classList.add('hidden');
    document.getElementById('modalContent').innerHTML = '';
    clearInterval(liveInterval);
}

function startLive(gameId) {
    liveInterval = setInterval(() => {
        fetch(`/viewer/game/${gameId}/live`)
            .then(res => res.json())
            .then(data => {
                // Live scores
                if(document.getElementById('liveScoreA')) {
                    document.getElementById('liveScoreA').innerText = data.score_a;
                }
                if(document.getElementById('liveScoreB')) {
                    document.getElementById('liveScoreB').innerText = data.score_b;
                }
            });
    }, 3000);
}
</script>
@endsection
