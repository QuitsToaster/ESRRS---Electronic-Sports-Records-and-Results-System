@extends('layouts.viewer')

@section('page-title', 'Live Score Viewer')

@section('content')

@php
    $scores = is_array($game->scores) ? $game->scores : (is_string($game->scores) ? json_decode($game->scores, true) : null);
    $scores = $scores ?? ['team_a' => [], 'team_b' => []];

    $status = $game->status ?? 'Upcoming';
    $statusColor = match($status) {
        'Upcoming' => 'bg-yellow-400',
        'Ongoing'  => 'bg-green-500',
        'Finished' => 'bg-gray-400',
        default    => 'bg-gray-400',
    };

    // Fetch divisions instead of schools
    $divisionA = \App\Models\Division::where('name', $game->team_a)->first();
    $divisionB = \App\Models\Division::where('name', $game->team_b)->first();

    $winner = $game->winner;
@endphp

<script>
window.scrollTo(0, 0);
</script>

<div class="relative min-h-screen overflow-hidden">

    {{-- Background --}}
    <div class="absolute inset-0 -z-10">
        <img src="{{ asset('storage/game1.jpg') }}" class="w-full h-full object-cover scale-105 brightness-75 blur-sm">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    </div>

    {{-- Back Button --}}
    <button onclick="history.back()"
        class="fixed top-20 left-4 md:left-6 z-[9999] inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700
               text-white font-bold rounded-full shadow-lg hover:scale-105 transition-transform text-sm" style="font-family: 'Teko', sans-serif;">
        ← Back
    </button>

    {{-- NBA Header Banner --}}
    <div class="w-full h-48 md:h-56 grid grid-cols-[1fr_1.5fr_1fr] relative">
        {{-- Team A Logo --}}
        <div class="relative overflow-hidden">
            @if($divisionA && $divisionA->logo)
                <div class="absolute inset-0 bg-cover bg-center {{ $winner === $game->team_a ? 'ring-4 ring-green-400' : '' }}"
                     style="background-image:url('{{ asset('storage/'.$divisionA->logo) }}')"></div>
            @endif
            <div class="absolute inset-0 bg-black/30"></div>
        </div>

        {{-- Center Score --}}
        <div class="bg-black flex flex-col items-center justify-center text-white text-center px-4">
            <span class="text-lg md:text-xl tracking-widest uppercase opacity-80" style="font-family: 'Teko', sans-serif;">
                {{ $game->event->name }}
            </span>

            <div class="flex items-center gap-4 md:gap-6 mt-2 text-6xl md:text-9xl font-extrabold">
                <span id="liveScoreA"
                      class="flex-1 {{ $winner === $game->team_a ? 'text-green-400' : 'text-gray-100' }}"
                      style="font-family: 'Teko', sans-serif;">
                    {{ $game->score_a ?? 0 }}
                </span>
                <span class="text-2xl md:text-3xl font-bold tracking-widest opacity-80">—</span>
                <span id="liveScoreB"
                      class="flex-1 {{ $winner === $game->team_b ? 'text-green-400' : 'text-gray-100' }}"
                      style="font-family: 'Teko', sans-serif;">
                    {{ $game->score_b ?? 0 }}
                </span>
            </div>

            <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                {{ $status }}
            </span>

            @if($status === 'Ongoing')
                <span class="mt-1 text-xs text-red-400 tracking-widest animate-pulse">
                    ● LIVE
                </span>
            @endif

            @if($winner)
                <span class="mt-1 text-sm font-bold text-green-400 tracking-wide">
                    Winner: {{ $winner }}
                </span>
            @endif
        </div>

        {{-- Team B Logo --}}
        <div class="relative overflow-hidden">
            @if($divisionB && $divisionB->logo)
                <div class="absolute inset-0 bg-cover bg-center {{ $winner === $game->team_b ? 'ring-4 ring-green-400' : '' }}"
                     style="background-image:url('{{ asset('storage/'.$divisionB->logo) }}')"></div>
            @endif
            <div class="absolute inset-0 bg-black/30"></div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="w-full px-2 md:px-4 py-10 space-y-12 relative z-10">

        {{-- Live Video --}}
        @if($game->live_video_url)
            <div class="bg-black/80 backdrop-blur shadow-lg p-4 rounded-lg">
                <h3 class="text-white font-semibold mb-2 text-lg md:text-xl">Live Stream</h3>
                <div class="aspect-w-16 aspect-h-9">
                    @php
                        $embedUrl = '';
                        $url = $game->live_video_url;
                        if (Str::contains($url, 'youtube')) {
                            $videoId = Str::after($url, 'v=');
                            $videoId = Str::before($videoId, '&');
                            $embedUrl = 'https://www.youtube.com/embed/'.$videoId;
                        }
                    @endphp
                    @if($embedUrl)
                        <iframe class="w-full h-64 md:h-96" src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                    @endif
                </div>
            </div>
        @endif

        {{-- Period Breakdown --}}
        <h3 class="text-4xl md:text-6xl font-bold text-white text-center mb-6" style="font-family: 'Teko', sans-serif;">
            Period Breakdown
        </h3>
        <div class="w-full bg-gray-900 shadow-lg p-4 md:p-6">
            <div class="flex flex-col gap-4">

                {{-- Team A --}}
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-white font-bold text-base text-2xl md:text-4xl" style="font-family: 'Teko', sans-serif;">
                        <span>{{ $game->team_a }}</span>
                        <span>{{ array_sum($scores['team_a'] ?? []) }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1 h-12">
                        @foreach($periods as $i => $p)
                            <div class="flex-1 min-w-[30px] bg-gray-700 flex items-center justify-center text-white font-semibold text-sm" style="font-family: 'Teko', sans-serif;">
                                {{ $scores['team_a'][$i] ?? 0 }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Team B --}}
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between text-white font-bold text-base text-2xl md:text-4xl" style="font-family: 'Teko', sans-serif;">
                        <span>{{ $game->team_b }}</span>
                        <span>{{ array_sum($scores['team_b'] ?? []) }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1 h-12">
                        @foreach($periods as $i => $p)
                            <div class="flex-1 min-w-[30px] bg-gray-700 flex items-center justify-center text-white font-semibold text-sm" style="font-family: 'Teko', sans-serif;">
                                {{ $scores['team_b'][$i] ?? 0 }}
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- Rosters --}}
        <h3 class="text-4xl md:text-6xl font-bold text-white text-center mb-6" style="font-family: 'Teko', sans-serif;">
            Rosters
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">

            {{-- Team A --}}
            <div class="bg-gray-900 shadow-lg p-4 md:p-6">
                <h3 class="text-white font-bold text-2xl md:text-6xl mb-6" style="font-family: 'Teko', sans-serif;">{{ $game->team_a }}</h3>
                <div class="flex flex-col gap-4">
                    @foreach($athletesA as $a)
                        @php
                            $fullName = $a->last_name . ', ' . $a->first_name;
                            if($a->middle_initial) $fullName .= ' ' . strtoupper(substr($a->middle_initial,0,1)) . '.';
                        @endphp
                        <div class="flex items-center gap-4 text-white p-2 md:p-3">
                            @if($a->profile_photo)
                                <img src="{{ asset('storage/' . $a->profile_photo) }}"
                                     alt="{{ $fullName }}"
                                     class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover bg-gray-700">
                            @else
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gray-700 flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($a->first_name,0,1) . substr($a->last_name,0,1)) }}
                                </div>
                            @endif
                            <span class="text-white font-semibold text-2xl md:text-2xl truncate" style="font-family: 'Teko', sans-serif;">
                                {{ $fullName }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Team B --}}
            <div class="bg-gray-900 shadow-lg p-4 md:p-6">
                <h3 class="text-white font-bold text-2xl md:text-6xl mb-6" style="font-family: 'Teko', sans-serif;">{{ $game->team_b }}</h3>
                <div class="flex flex-col gap-4">
                    @foreach($athletesB as $a)
                        @php
                            $fullName = $a->last_name . ', ' . $a->first_name;
                            if($a->middle_initial) $fullName .= ' ' . strtoupper(substr($a->middle_initial,0,1)) . '.';
                        @endphp
                        <div class="flex items-center gap-4 text-white p-2 md:p-3">
                            @if($a->profile_photo)
                                <img src="{{ asset('storage/' . $a->profile_photo) }}"
                                     alt="{{ $fullName }}"
                                     class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover bg-gray-700">
                            @else
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gray-700 flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($a->first_name,0,1) . substr($a->last_name,0,1)) }}
                                </div>
                            @endif
                            <span class="text-white font-semibold text-2xl md:text-2xl truncate" style="font-family: 'Teko', sans-serif;">
                                {{ $fullName }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Live scripts remain unchanged --}}
<script>
setInterval(async () => {
    try {
        const res = await fetch("{{ route('viewer.liveGame', $game->id) }}");
        const data = await res.json();

        document.getElementById('liveScoreA').innerText = data.score_a;
        document.getElementById('liveScoreB').innerText = data.score_b;

    } catch (e) {
        console.error('Live score fetch failed', e);
    }
}, 1000);
</script>

@endsection
