@extends('layouts.viewer')

@section('page-title', 'Games')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Game Schedule</h2>
    <p class="text-gray-600 text-sm">Browse upcoming, ongoing, and finished games.</p>
</div>

@if($games->isEmpty())
    <div class="text-center py-12 text-gray-500">
        No games scheduled yet.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($games as $game)
            <div class="bg-white rounded-lg shadow-lg p-5 hover:shadow-xl transition">
                {{-- Event --}}
                <h3 class="text-lg font-semibold text-blue-600 mb-2">
                    {{ $game->event->name ?? 'No Event' }}
                </h3>

                {{-- Teams --}}
                <div class="flex justify-between items-center mb-3">
                    <div class="text-gray-800 font-semibold">
                        {{ $game->team_a }} 
                        @if(!is_null($game->score_a))
                            <span class="text-blue-500 font-bold">({{ $game->score_a }})</span>
                        @endif
                    </div>
                    <span class="text-gray-400">vs</span>
                    <div class="text-gray-800 font-semibold">
                        {{ $game->team_b }} 
                        @if(!is_null($game->score_b))
                            <span class="text-blue-500 font-bold">({{ $game->score_b }})</span>
                        @endif
                    </div>
                </div>

                {{-- Status --}}
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

                {{-- Date / Time --}}
                <p class="text-gray-500 text-sm mb-1">
                    <span class="font-semibold">Date & Time:</span> 
                    {{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}
                </p>

                {{-- Venue --}}
                <p class="text-gray-500 text-sm">
                    <span class="font-semibold">Venue:</span> {{ $game->venue }}
                </p>
            </div>
        @endforeach
    </div>
@endif
@endsection
