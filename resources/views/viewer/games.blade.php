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
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    </div>

    {{-- CONTENT --}}
    <div class="max-w-7xl mx-auto px-4 py-10 space-y-6 relative z-10">

        <h2 class="text-7xl font-bold text-white tracking-widest uppercase"
            style="font-family: 'Teko', sans-serif;">
            Game Schedule
        </h2>

        {{-- RESPONSIVE GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($games as $game)

                @php
                    $divisionA = \App\Models\Division::where('name', $game->team_a)->first();
                    $divisionB = \App\Models\Division::where('name', $game->team_b)->first();

                    $winner = $game->winner;

                    $isWinnerA = $winner && $winner === $game->team_a;
                    $isWinnerB = $winner && $winner === $game->team_b;

                    $status = $game->status ?? 'Upcoming';

                    if ($status === 'Upcoming') {
                        $statusClass = 'bg-yellow-100 text-yellow-800';
                    } elseif ($status === 'Ongoing') {
                        $statusClass = 'bg-green-100 text-green-800';
                    } else {
                        $statusClass = 'bg-gray-200 text-gray-800';
                    }
                @endphp

                {{-- GAME CARD --}}
<div onclick="window.location.href='{{ route('viewer.game.modal', $game->id) }}'"
     class="group cursor-pointer bg-white overflow-hidden relative
     shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl
     {{ $winner ? 'ring-2 ring-yellow-400' : '' }}">


                    {{-- STATUS --}}
                    <span class="absolute top-3 right-3 z-20 px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                        {{ $status }}
                    </span>

                    {{-- MATCH LAYOUT --}}
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_1.6fr_1fr] h-auto md:h-40">

                        {{-- TEAM A --}}
                        <div class="relative overflow-hidden bg-black h-32 md:h-full
                            {{ $isWinnerA ? 'ring-4 ring-yellow-400' : '' }}">
                            @if($divisionA && $divisionA->logo)
                                <div class="absolute inset-0 bg-cover bg-center
                                    transition-transform duration-500 group-hover:scale-110"
                                     style="background-image:url('{{ asset('storage/' . $divisionA->logo) }}')"></div>
                            @endif

                            @if($isWinnerA)
                                <span class="absolute top-2 left-2 bg-yellow-400 text-black text-xs px-2 py-1 rounded font-bold">
                                    👑 WINNER
                                </span>
                            @endif
                        </div>

                        {{-- CENTER --}}
                        <div class="bg-black text-white flex flex-col items-center justify-center text-center px-4"
                             style="font-family: 'Teko', sans-serif;">
                            <p class="text-xs tracking-widest uppercase opacity-80">
                                {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y') }}
                            </p>
                            <p class="text-4xl md:text-6xl font-bold tracking-wider">
                                {{ \Carbon\Carbon::parse($game->game_time)->format('h:i A') }}
                            </p>
                            <div class="w-12 h-[2px] bg-white my-2 opacity-80"></div>
                            <p class="text-[10px] uppercase tracking-[0.25em] opacity-70">
                                {{ $game->event->name }}
                            </p>
                        </div>

                        {{-- TEAM B --}}
                        <div class="relative overflow-hidden bg-black h-32 md:h-full
                            {{ $isWinnerB ? 'ring-4 ring-yellow-400' : '' }}">
                            @if($divisionB && $divisionB->logo)
                                <div class="absolute inset-0 bg-cover bg-center
                                    transition-transform duration-500 group-hover:scale-110"
                                     style="background-image:url('{{ asset('storage/' . $divisionB->logo) }}')"></div>
                            @endif

                            @if($isWinnerB)
                                <span class="absolute top-2 right-2 bg-yellow-400 text-black text-xs px-2 py-1 rounded font-bold">
                                    👑 WINNER
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- TEAM NAMES --}}
                    <div class="grid grid-cols-3 text-center py-2 bg-gray-100"
                         style="font-family: 'Teko', sans-serif;">

                        <span class="font-semibold {{ $isWinnerA ? 'text-yellow-600' : '' }}">
                            {{ $game->team_a }}
                        </span>

                        <span class="text-gray-400 tracking-widest">VS</span>

                        <span class="font-semibold {{ $isWinnerB ? 'text-yellow-600' : '' }}">
                            {{ $game->team_b }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>



@endsection
