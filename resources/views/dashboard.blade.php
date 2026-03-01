@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">

    {{-- Divisions Card --}}
    <a href="{{ route('divisions.index') }}" class="block bg-blue-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-blue-800">Divisions</h2>
                <p class="text-gray-700 mt-2">Total number of participating divisions</p>
            </div>
            <div class="text-3xl font-bold text-blue-800">{{ $divisionsCount }}</div>
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

    <!-- {{-- Coaches Card --}}
    <a href="{{ route('coaches.index') }}" class="block bg-red-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-red-800">Coaches</h2>
                <p class="text-gray-700 mt-2">Total number of registered coaches</p>
            </div>
            <div class="text-3xl font-bold text-red-800">{{ $coachesCount }}</div>
        </div>
    </a> -->

    <!-- {{-- Total Games Card --}}
    <a href="{{ route('games.index') }}" class="block bg-teal-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-teal-800">Games</h2>
                <p class="text-gray-700 mt-2">Total number of games</p>
            </div>
            <div class="text-3xl font-bold text-teal-800">{{ $gamesCount }}</div>
        </div>
    </a> -->

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


<!-- {{-- Live Games --}}
<div class="mt-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Games</h2>

    @if($liveGames->isEmpty())
        <div class="text-gray-500 text-center py-6">No upcoming or ongoing games.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($liveGames as $game)
                <a href="{{ route('games.index') }}" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4 cursor-pointer">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $game->event->name ?? '-' }}</h3>
                    
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold @if($game->winner === $game->team_a) text-emerald-600 font-bold @endif">
                            {{ $game->team_a }}
                        </span>
                        <span class="font-bold">VS</span>
                        <span class="font-bold @if($game->winner === $game->team_b) text-emerald-600 font-bold @endif">
                            {{ $game->team_b }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span class="@if($game->status === 'Upcoming') text-blue-600 @elseif($game->status === 'Ongoing') text-green-600 @else text-gray-600 @endif font-semibold">
                            {{ $game->status }}
                        </span>
                        <span>{{ $game->game_time->format('M d, Y h:i A') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div> -->



@php
use App\Models\AllAwardResult;
use App\Models\Division;

/* =========================================================
 | REGULAR MEDAL TALLY (Elementary + Secondary)
 ========================================================= */
$regularRaw = AllAwardResult::select('sdo','medal', \DB::raw('SUM(`count`) as total'))
    ->where('status','approved')
    ->whereIn('division',['Elementary','Secondary'])
    ->groupBy('sdo','medal')
    ->get()
    ->groupBy('sdo');

$medalTallyRegular = Division::orderBy('name')->get()->map(function($division) use ($regularRaw) {
    $rows = $regularRaw->get($division->name, collect());

    $gold   = $rows->where('medal','Gold')->sum('total');
    $silver = $rows->where('medal','Silver')->sum('total');
    $bronze = $rows->where('medal','Bronze')->sum('total');

    return (object)[
        'name'   => $division->name,
        'logo'   => $division->logo,
        'gold'   => $gold,
        'silver' => $silver,
        'bronze' => $bronze,
        'total'  => $gold + $silver + $bronze,
        'results'=> AllAwardResult::with(['sport','event'])
                        ->whereIn('division',['Elementary','Secondary'])
                        ->where('sdo',$division->name)
                        ->get(),
    ];
})->sort(function($a,$b){
    if($a->gold !== $b->gold) return $b->gold <=> $a->gold;
    if($a->silver !== $b->silver) return $b->silver <=> $a->silver;
    return $b->bronze <=> $a->bronze;
})->values();

/* =========================================================
 | PARA GAMES MEDAL TALLY
 ========================================================= */
$paraRaw = AllAwardResult::select('sdo','medal', \DB::raw('SUM(`count`) as total'))
    ->where('status','approved')
    ->where('division','Paragames')
    ->groupBy('sdo','medal')
    ->get()
    ->groupBy('sdo');

$medalTallyPara = Division::orderBy('name')->get()->map(function($division) use ($paraRaw) {
    $rows = $paraRaw->get($division->name, collect());

    $gold   = $rows->where('medal','Gold')->sum('total');
    $silver = $rows->where('medal','Silver')->sum('total');
    $bronze = $rows->where('medal','Bronze')->sum('total');

    return (object)[
        'name'   => $division->name,
        'logo'   => $division->logo,
        'gold'   => $gold,
        'silver' => $silver,
        'bronze' => $bronze,
        'total'  => $gold + $silver + $bronze,
        'results'=> AllAwardResult::with(['sport','event'])
                        ->where('division','Paragames')
                        ->where('sdo',$division->name)
                        ->get(),
    ];
})->sort(function($a,$b){
    if($a->gold !== $b->gold) return $b->gold <=> $a->gold;
    if($a->silver !== $b->silver) return $b->silver <=> $a->silver;
    return $b->bronze <=> $a->bronze;
})->values();
@endphp

{{-- ================= REGULAR MEDAL TALLY ================= --}}
<div class="mt-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Medal Tally (Regular)</h2>

    @if($medalTallyRegular->isEmpty())
        <div class="text-gray-500 text-center py-6">No medal data available.</div>
    @else
        <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Gold</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Silver</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bronze</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medalTallyRegular as $index => $division)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Rank --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ $index + 1 }}
                            </td>

                            {{-- Division --}}
                            <td class="px-6 py-4 flex items-center space-x-3">
                                @if($division->logo)
                                    <img src="{{ asset('storage/' . $division->logo) }}" alt="{{ $division->name }} Logo"
                                         class="w-10 h-10 object-cover rounded-full border">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400 font-bold">
                                        {{ strtoupper(substr($division->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-semibold text-gray-800">{{ $division->name }}</span>
                            </td>

                            {{-- Gold --}}
                            <td class="px-6 py-4 text-center text-yellow-500 font-bold">{{ $division->gold }}</td>

                            {{-- Silver --}}
                            <td class="px-6 py-4 text-center text-gray-400 font-bold">{{ $division->silver }}</td>

                            {{-- Bronze --}}
                            <td class="px-6 py-4 text-center text-orange-500 font-bold">{{ $division->bronze }}</td>

                            {{-- Total --}}
                            <td class="px-6 py-4 text-center text-gray-700 font-bold">{{ $division->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ================= PARAGAMES MEDAL TALLY ================= --}}
<div class="mt-12">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Paragames Medal Tally</h2>

    @if($medalTallyPara->isEmpty())
        <div class="text-gray-500 text-center py-6">No medal data available.</div>
    @else
        <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Gold</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Silver</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bronze</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medalTallyPara as $index => $division)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Rank --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ $index + 1 }}
                            </td>

                            {{-- Division --}}
                            <td class="px-6 py-4 flex items-center space-x-3">
                                @if($division->logo)
                                    <img src="{{ asset('storage/' . $division->logo) }}" alt="{{ $division->name }} Logo"
                                         class="w-10 h-10 object-cover rounded-full border">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400 font-bold">
                                        {{ strtoupper(substr($division->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-semibold text-gray-800">{{ $division->name }}</span>
                            </td>

                            {{-- Gold --}}
                            <td class="px-6 py-4 text-center text-yellow-500 font-bold">{{ $division->gold }}</td>

                            {{-- Silver --}}
                            <td class="px-6 py-4 text-center text-gray-400 font-bold">{{ $division->silver }}</td>

                            {{-- Bronze --}}
                            <td class="px-6 py-4 text-center text-orange-500 font-bold">{{ $division->bronze }}</td>

                            {{-- Total --}}
                            <td class="px-6 py-4 text-center text-gray-700 font-bold">{{ $division->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>





@endsection
