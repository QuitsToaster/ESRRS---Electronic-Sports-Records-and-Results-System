@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">

    {{-- Schools Card --}}
    <div class="bg-blue-100 dark:bg-blue-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Schools</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Total number of participating schools</p>
            </div>
            <div class="text-3xl font-bold text-blue-800 dark:text-blue-200">{{ $schoolsCount }}</div>
        </div>
    </div>

    {{-- Athletes Card --}}
    <div class="bg-green-100 dark:bg-green-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-green-800 dark:text-green-200">Athletes</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Total number of registered athletes</p>
            </div>
            <div class="text-3xl font-bold text-green-800 dark:text-green-200">{{ $athletesCount }}</div>
        </div>
    </div>

    {{-- Events Card --}}
    <div class="bg-yellow-100 dark:bg-yellow-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Events</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Total number of scheduled events</p>
            </div>
            <div class="text-3xl font-bold text-yellow-800 dark:text-yellow-200">{{ $eventsCount }}</div>
        </div>
    </div>

    {{-- Sports Card --}}
    <div class="bg-purple-100 dark:bg-purple-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-purple-800 dark:text-purple-200">Sports</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Total number of sports categories</p>
            </div>
            <div class="text-3xl font-bold text-purple-800 dark:text-purple-200">{{ $sportsCount }}</div>
        </div>
    </div>

    {{-- Coaches Card --}}
    <div class="bg-red-100 dark:bg-red-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-red-800 dark:text-red-200">Coaches</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Total number of registered coaches</p>
            </div>
            <div class="text-3xl font-bold text-red-800 dark:text-red-200">{{ $coachesCount }}</div>
        </div>
    </div>

    {{-- Total Games Card --}}
    <div class="bg-teal-100 dark:bg-teal-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-teal-800 dark:text-teal-200">Games</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Total number of games</p>
            </div>
            <div class="text-3xl font-bold text-teal-800 dark:text-teal-200">{{ $gamesCount }}</div>
        </div>
    </div>

    {{-- Ongoing Games Card --}}
    <div class="bg-orange-100 dark:bg-orange-900 shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-orange-800 dark:text-orange-200">Ongoing Games</h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2">Games currently in progress</p>
            </div>
            <div class="text-3xl font-bold text-orange-800 dark:text-orange-200">{{ $ongoingGamesCount }}</div>
        </div>
    </div>

</div>

{{-- Live Games Table --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto p-4 mb-6">

    <h2 class="text-xl font-semibold text-gray-800 mb-4">Live Games</h2>

    @if($liveGames->isEmpty())
        <div class="text-gray-500 dark:text-gray-300 text-center py-6">No upcoming or ongoing games.</div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-800">Event</th>
                        <th class="px-6 py-3 text-left text-gray-800">Team A</th>
                        <th class="px-6 py-3 text-left text-gray-800">Team B</th>
                        <th class="px-6 py-3 text-left text-gray-800">Score</th>
                        <th class="px-6 py-3 text-left text-gray-800">Status</th>
                        <th class="px-6 py-3 text-left text-gray-800">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($liveGames as $game)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">{{ $game->event->name ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $game->team_a }}</td>
                            <td class="px-6 py-3">{{ $game->team_b }}</td>
                            <td class="px-6 py-3 font-bold">{{ $game->score_a ?? 0 }} - {{ $game->score_b ?? 0 }}</td>
                            <td class="px-6 py-3">
                                <span class="
                                    @if($game->current_status === 'Upcoming') text-blue-600
                                    @elseif($game->current_status === 'Ongoing') text-green-600
                                    @else text-gray-600
                                    @endif
                                    font-semibold">
                                    {{ $game->current_status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $game->game_time->format('M d, Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Real-Time Medal Tally --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-x-auto p-4">

    <h2 class="text-xl font-semibold text-gray-800 mb-4">Medal Tally</h2>

    @if($medalTally->isEmpty())
        <div class="text-gray-500 dark:text-gray-300 text-center py-6">No medal data available.</div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-800">School</th>
                        <th class="px-6 py-3 text-left text-gray-800">Gold</th>
                        <th class="px-6 py-3 text-left text-gray-800">Silver</th>
                        <th class="px-6 py-3 text-left text-gray-800">Bronze</th>
                        <th class="px-6 py-3 text-left text-gray-800">Total</th>
                        <th class="px-6 py-3 text-left text-gray-800">Rank</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($medalTally as $school)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">{{ $school->name }}</td>
                            <td class="px-6 py-3 font-bold text-yellow-500">{{ $school->gold }}</td>
                            <td class="px-6 py-3 font-bold text-gray-400">{{ $school->silver }}</td>
                            <td class="px-6 py-3 font-bold text-orange-500">{{ $school->bronze }}</td>
                            <td class="px-6 py-3 font-bold">{{ $school->total }}</td>
                            <td class="px-6 py-3 font-semibold">{{ $school->rank }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>


@endsection
