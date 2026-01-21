@extends('layouts.viewer')

@section('page-title', 'Home')

@section('content')
<div class="space-y-8 px-4 md:px-8 lg:px-16">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl shadow-lg p-8 transform transition duration-500 hover:scale-105">
        <h2 class="text-3xl md:text-4xl font-bold mb-2 animate-fade-in">Welcome!</h2>
        <p class="text-gray-100 text-lg md:text-xl animate-fade-in delay-200">Quick overview of the latest events, games, announcements, and medal tallies.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transform transition hover:scale-105 hover:shadow-2xl">
            <span class="text-3xl font-bold animate-pulse">{{ $eventsCount }}</span>
            <span class="mt-2 uppercase tracking-wide font-semibold">Events</span>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transform transition hover:scale-105 hover:shadow-2xl">
            <span class="text-3xl font-bold animate-pulse">{{ $gamesCount }}</span>
            <span class="mt-2 uppercase tracking-wide font-semibold">Games</span>
        </div>
        <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transform transition hover:scale-105 hover:shadow-2xl">
            <span class="text-3xl font-bold animate-pulse">{{ $announcementsCount }}</span>
            <span class="mt-2 uppercase tracking-wide font-semibold">Announcements</span>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-700 text-white rounded-xl shadow-lg p-6 flex flex-col items-center transform transition hover:scale-105 hover:shadow-2xl">
            <span class="text-3xl font-bold animate-pulse">{{ $medalTally->count() }}</span>
            <span class="mt-2 uppercase tracking-wide font-semibold">Schools with Medals</span>
        </div>
    </div>

    {{-- Upcoming Events & Games --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Upcoming Events --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 transform transition hover:scale-105 hover:shadow-2xl">
            <h3 class="text-2xl font-semibold mb-4 text-gray-800 border-b pb-2">Upcoming Events</h3>
            @if($upcomingEvents->isEmpty())
                <p class="text-gray-500">No upcoming events.</p>
            @else
                <ul class="space-y-2">
                    @foreach($upcomingEvents as $event)
                        <li class="border border-gray-200 rounded-lg p-3 hover:bg-blue-50 transition flex justify-between items-center">
                            <span class="font-semibold text-gray-700">{{ $event->name }}</span>
                            <span class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Upcoming Games --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 transform transition hover:scale-105 hover:shadow-2xl">
            <h3 class="text-2xl font-semibold mb-4 text-gray-800 border-b pb-2">Upcoming Games</h3>
            @if($upcomingGames->isEmpty())
                <p class="text-gray-500">No upcoming games.</p>
            @else
                <ul class="space-y-2">
                    @foreach($upcomingGames as $game)
                        <li class="border border-gray-200 rounded-lg p-3 hover:bg-green-50 transition flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-gray-700">{{ $game->team_a }} vs {{ $game->team_b }}</span>
                                <span class="text-gray-400 text-sm ml-1">({{ $game->event->name ?? 'No Event' }})</span>
                            </div>
                            <span class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($game->game_time)->format('M d, Y h:i A') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Latest Announcements --}}
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 transform transition hover:scale-105 hover:shadow-2xl">
        <h3 class="text-2xl font-semibold mb-4 text-gray-800 border-b pb-2">Latest Announcements</h3>
        @if($latestAnnouncements->isEmpty())
            <p class="text-gray-500">No announcements yet.</p>
        @else
            <ul class="space-y-2">
                @foreach($latestAnnouncements as $announcement)
                    <li class="border border-gray-200 rounded-lg p-4 hover:bg-yellow-50 transition">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-blue-600">{{ $announcement->title }}</span>
                            <span class="text-gray-400 text-sm">{{ $announcement->published_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-gray-700 mt-1">{{ \Illuminate\Support\Str::limit($announcement->content, 120, '...') }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Medal Tally --}}
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 transform transition hover:scale-105 hover:shadow-2xl">
        <h3 class="text-2xl font-semibold mb-4 text-gray-800 border-b pb-2">Medal Tally</h3>
        @if($medalTally->isEmpty())
            <p class="text-gray-500">No medals awarded yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left table-auto border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">School</th>
                            <th class="px-4 py-2">Gold</th>
                            <th class="px-4 py-2">Silver</th>
                            <th class="px-4 py-2">Bronze</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medalTally as $school)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-2">{{ \App\Models\School::find($school->school_id)->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-2 text-yellow-500 font-semibold">{{ $school->gold }}</td>
                                <td class="px-4 py-2 text-gray-400 font-semibold">{{ $school->silver }}</td>
                                <td class="px-4 py-2 text-orange-500 font-semibold">{{ $school->bronze }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Animations --}}
<style>
    .animate-fade-in {
        opacity: 0;
        animation: fadeIn 1s forwards;
    }
    .animate-fade-in.delay-200 { animation-delay: 0.2s; }
    .animate-fade-in.delay-400 { animation-delay: 0.4s; }

    @keyframes fadeIn {
        to { opacity: 1; }
    }
</style>
@endsection
