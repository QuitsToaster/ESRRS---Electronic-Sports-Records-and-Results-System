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
            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                   placeholder="Search games by team or event..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('viewer.games') }}" class="text-gray-500 hover:text-gray-700 px-2 py-2 transition">
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
                <div class="bg-white rounded-lg shadow hover:shadow-xl transition transform hover:-translate-y-1 p-5 flex flex-col justify-between">

                    {{-- Event Name --}}
                    <h3 class="text-lg font-semibold text-blue-600 mb-3">
                        {{ $game->event->name ?? 'No Event' }}
                    </h3>

                    {{-- Teams & Scores --}}
                    <div class="flex justify-between items-center mb-3">
                        <div class="text-gray-800 font-semibold flex items-center gap-1">
                            {{ $game->team_a }}
                            @if(!is_null($game->score_a))
                                <span class="text-blue-500 font-bold">({{ $game->score_a }})</span>
                            @endif
                        </div>
                        <span class="text-gray-400 font-semibold">vs</span>
                        <div class="text-gray-800 font-semibold flex items-center gap-1">
                            {{ $game->team_b }}
                            @if(!is_null($game->score_b))
                                <span class="text-blue-500 font-bold">({{ $game->score_b }})</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mb-2">
                        @php
                            $statusColors = [
                                'Upcoming' => 'bg-yellow-100 text-yellow-800',
                                'Ongoing' => 'bg-green-100 text-green-800',
                                'Finished' => 'bg-gray-100 text-gray-800'
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$game->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $game->status }}
                        </span>
                    </div>

                    {{-- Date & Venue --}}
                    <p class="text-gray-500 text-sm mb-1">
                        <span class="font-semibold">Date & Time:</span> 
                        {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}
                    </p>
                    <p class="text-gray-500 text-sm">
                        <span class="font-semibold">Venue:</span> {{ $game->venue ?? 'TBD' }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
