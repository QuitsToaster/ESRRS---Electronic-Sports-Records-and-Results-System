@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">

    {{-- Schools Card --}}
    <a href="{{ route('schools.index') }}" class="block bg-blue-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-blue-800">Schools</h2>
                <p class="text-gray-700 mt-2">Total number of participating schools</p>
            </div>
            <div class="text-3xl font-bold text-blue-800">{{ $schoolsCount }}</div>
        </div>
    </a>

    {{-- Athletes Card --}}
    <a href="{{ route('athletes.index') }}" class="block bg-green-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-green-800">Athletes</h2>
                <p class="text-gray-700 mt-2">Total number of registered athletes</p>
            </div>
            <div class="text-3xl font-bold text-green-800">{{ $athletesCount }}</div>
        </div>
    </a>

    {{-- Events Card --}}
    <a href="{{ route('events.index') }}" class="block bg-yellow-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-yellow-800">Events</h2>
                <p class="text-gray-700 mt-2">Total number of scheduled events</p>
            </div>
            <div class="text-3xl font-bold text-yellow-800">{{ $eventsCount }}</div>
        </div>
    </a>

    {{-- Sports Card --}}
    <a href="{{ route('sports.index') }}" class="block bg-purple-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-purple-800">Sports</h2>
                <p class="text-gray-700 mt-2">Total number of sports categories</p>
            </div>
            <div class="text-3xl font-bold text-purple-800">{{ $sportsCount }}</div>
        </div>
    </a>

    {{-- Coaches Card --}}
    <a href="{{ route('coaches.index') }}" class="block bg-red-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-red-800">Coaches</h2>
                <p class="text-gray-700 mt-2">Total number of registered coaches</p>
            </div>
            <div class="text-3xl font-bold text-red-800">{{ $coachesCount }}</div>
        </div>
    </a>

    {{-- Total Games Card --}}
    <a href="{{ route('games.index') }}" class="block bg-teal-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-teal-800">Games</h2>
                <p class="text-gray-700 mt-2">Total number of games</p>
            </div>
            <div class="text-3xl font-bold text-teal-800">{{ $gamesCount }}</div>
        </div>
    </a>

    {{-- Ongoing Games Card --}}
    <a href="{{ route('games.index') }}" class="block bg-orange-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-orange-800">Ongoing Games</h2>
                <p class="text-gray-700 mt-2">Games currently in progress</p>
            </div>
            <div class="text-3xl font-bold text-orange-800">{{ $ongoingGamesCount }}</div>
        </div>
    </a>

</div>

{{-- Newest Announcements --}}
<div class="mt-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Newest Announcements</h2>

    @if($latestAnnouncements->isEmpty())
        <div class="text-gray-500 text-center py-6">No announcements available.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($latestAnnouncements as $announcement)
                <a href="{{ route('announcements.index') }}" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4 flex flex-col justify-between cursor-pointer">
                    <div class="flex flex-col gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $announcement->title }}</h3>
                            <p class="text-sm text-gray-500 mb-1">
                                <span class="font-bold text-black">{{ $announcement->type }}</span>
                                @if($announcement->event)
                                    | Event: {{ $announcement->event->name }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit($announcement->content, 80) }}</p>
                        </div>

                        {{-- Attachments Preview --}}
                        @if($announcement->files->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($announcement->files as $file)
                                    @php
                                        $extension = strtolower($file->file_type);
                                        $url = asset('storage/' . $file->file_path);
                                    @endphp

                                    @if(in_array($extension, ['jpg','jpeg','png','gif']))
                                        <img src="{{ $url }}" alt="Attachment" class="w-10 h-10 object-cover rounded border border-gray-200">
                                    @else
                                        {{-- Non-image files just show type label --}}
                                        <span class="w-10 h-10 flex items-center justify-center bg-red-600 text-white rounded border border-gray-200 text-xs font-bold">
                                            {{ strtoupper($extension) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                        <span>By: {{ $announcement->user->name ?? 'System' }}</span>
                        <span>{{ $announcement->published_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>


{{-- Live Games --}}
<div class="mt-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Live Games</h2>

    @if($liveGames->isEmpty())
        <div class="text-gray-500 text-center py-6">No upcoming or ongoing games.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($liveGames as $game)
                <a href="{{ route('games.index') }}" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4 cursor-pointer">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $game->event->name ?? '-' }}</h3>
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium">{{ $game->team_a }}</span>
                        <span class="font-bold">{{ $game->score_a ?? 0 }} - {{ $game->score_b ?? 0 }}</span>
                        <span class="font-medium">{{ $game->team_b }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span class="@if($game->current_status === 'Upcoming') text-blue-600 @elseif($game->current_status === 'Ongoing') text-green-600 @else text-gray-600 @endif font-semibold">
                            {{ $game->current_status }}
                        </span>
                        <span>{{ $game->game_time->format('M d, Y h:i A') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Medal Tally --}}
<div class="mt-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Medal Tally</h2>

    @if($medalTally->isEmpty())
        <div class="text-gray-500 text-center py-6">No medal data available.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($medalTally as $school)
                <a href="{{ route('schools.index') }}" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4 cursor-pointer">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $school->name }}</h3>
                    <div class="flex justify-between items-center text-sm text-gray-500 mb-2">
                        <span class="font-bold text-black">Rank: {{ $school->rank }}</span>
                        <span class="font-bold">Total Medals: {{ $school->total }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="font-semibold text-yellow-500">Gold: {{ $school->gold }}</span>
                        <span class="font-semibold text-gray-400">Silver: {{ $school->silver }}</span>
                        <span class="font-semibold text-orange-500">Bronze: {{ $school->bronze }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

@endsection
