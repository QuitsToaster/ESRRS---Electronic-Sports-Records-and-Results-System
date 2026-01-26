@extends('layouts.viewer')

@section('page-title', 'Live Score Viewer')

@section('content')

@php
use Carbon\Carbon;

$scores = json_decode($game->scores, true) ?? ['team_a'=>[], 'team_b'=>[]];

$now = Carbon::now();
$gameStart = Carbon::parse($game->game_time);
$gameEnd = $gameStart->copy()->addMinutes($game->duration ?? 120); // default 2 hours

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
                <span class="text-sm uppercase tracking-wide text-gray-300">
                    {{ $game->team_a }}
                </span>
                <span id="liveScoreA"
                      class="text-6xl md:text-7xl font-extrabold text-green-400">
                    {{ $game->score_a ?? 0 }}
                </span>
            </div>

            {{-- VS / STATUS --}}
            <div class="flex flex-col items-center">
                {{-- Status Label --}}
                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }} mb-1">
                    {{ $status }}
                </span>

                {{-- Live Indicator (only flicker if ongoing) --}}
                @if($status === 'Ongoing')
                    <span class="text-xs text-red-400 font-semibold tracking-widest mb-1 animate-pulse">
                        ● LIVE
                    </span>
                @endif

                <span class="text-gray-300 text-sm">
                    {{ $game->event->name }}
                </span>
            </div>

            {{-- TEAM B --}}
            <div class="flex flex-col items-center w-1/3">
                <span class="text-sm uppercase tracking-wide text-gray-300">
                    {{ $game->team_b }}
                </span>
                <span id="liveScoreB"
                      class="text-6xl md:text-7xl font-extrabold text-red-400">
                    {{ $game->score_b ?? 0 }}
                </span>
            </div>

        </div>
    </div>

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

@endsection
