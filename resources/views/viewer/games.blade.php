@extends('layouts.viewer')

@section('page-title', 'Games')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Game Schedule</h2>
            <p class="text-gray-600 mt-1 text-sm">Browse upcoming, ongoing, and finished games.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('viewer.games') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text"
                   name="search"
                   value="{{ request('search') ?? '' }}"
                   placeholder="Search games by team or event..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2
                          focus:outline-none focus:ring-2 focus:ring-blue-400">

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold">
                Search
            </button>

            @if(request('search'))
                <a href="{{ route('viewer.games') }}"
                   class="text-gray-500 hover:text-gray-700 px-2 py-2">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Games Grid --}}
    @if($games->isEmpty())
        <div class="text-center py-12 text-gray-500">No games scheduled yet.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($games as $game)

                @php
                    $schoolA = \App\Models\School::where('name', $game->team_a)->first();
                    $schoolB = \App\Models\School::where('name', $game->team_b)->first();

                    $now = \Carbon\Carbon::now();
                    $gameTime = \Carbon\Carbon::parse($game->game_time);

                    // Assume a game lasts 2 hours (adjust if needed)
                    $gameEnd = $gameTime->copy()->addHours(2);

                    if ($now->lt($gameTime)) {
                        $computedStatus = 'Upcoming';
                    } elseif ($now->between($gameTime, $gameEnd)) {
                        $computedStatus = 'Ongoing';
                    } else {
                        $computedStatus = 'Finished';
                    }

                    $statusColors = [
                        'Upcoming' => 'bg-yellow-100 text-yellow-800',
                        'Ongoing'  => 'bg-green-100 text-green-800',
                        'Finished' => 'bg-gray-200 text-gray-800',
                    ];
                @endphp

                <div class="bg-white rounded-lg shadow hover:shadow-xl transition p-5 flex flex-col justify-between">

                    {{-- Event Name --}}
                    <h3 class="text-lg font-semibold text-blue-600 mb-4">
                        {{ $game->event->name ?? 'No Event' }}
                    </h3>

                    {{-- Teams --}}
                    <div class="flex justify-between items-center mb-4">

                        {{-- Team A --}}
                        <div class="flex flex-col items-center w-1/3 text-center">
                            @if($schoolA && $schoolA->logo)
                                <img src="{{ asset('storage/' . $schoolA->logo) }}"
                                     class="h-12 w-12 object-contain mb-1">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-xs">
                                    N/A
                                </div>
                            @endif

                            <span class="font-semibold text-sm">{{ $game->team_a }}</span>

                            @if(!is_null($game->score_a))
                                <span class="text-blue-600 font-bold text-sm">
                                    {{ $game->score_a }}
                                </span>
                            @endif
                        </div>

                        <div class="text-gray-400 font-bold text-sm">VS</div>

                        {{-- Team B --}}
                        <div class="flex flex-col items-center w-1/3 text-center">
                            @if($schoolB && $schoolB->logo)
                                <img src="{{ asset('storage/' . $schoolB->logo) }}"
                                     class="h-12 w-12 object-contain mb-1">
                            @else
                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-xs">
                                    N/A
                                </div>
                            @endif

                            <span class="font-semibold text-sm">{{ $game->team_b }}</span>

                            @if(!is_null($game->score_b))
                                <span class="text-blue-600 font-bold text-sm">
                                    {{ $game->score_b }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Computed Status --}}
                    <div class="mb-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$computedStatus] }}">
                            {{ $computedStatus }}
                        </span>
                    </div>

                    {{-- Date & Venue --}}
                    <p class="text-gray-500 text-sm mb-1">
                        <span class="font-semibold">Date & Time:</span>
                        {{ $gameTime->format('M d, Y h:i A') }}
                    </p>

                    <p class="text-gray-500 text-sm">
                        <span class="font-semibold">Venue:</span>
                        {{ $game->venue ?? 'TBD' }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
