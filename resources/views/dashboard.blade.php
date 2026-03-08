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


    {{-- Users Card ✅ NEW --}}
    <a href="{{ route('users.index') }}" class="block bg-indigo-100 shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-indigo-800">Accounts</h2>
                <p class="text-gray-700 mt-2">Total number of system users</p>
            </div>
            <div class="text-3xl font-bold text-indigo-800">{{ $usersCount }}</div>
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
{{-- ============================================================= --}}
{{-- 🏆 AWARDS STATUS OVERVIEW (Clickable with Modal) --}}
{{-- ============================================================= --}}

<div x-data="{ openModal: false, modalTitle: '', modalAwards: [] }" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    {{-- ALL AWARDS STATUS --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow p-6">

        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            All Awards Status
        </h2>

        <div class="grid grid-cols-3 gap-4">

            {{-- Pending --}}
            <div @click="openModal = true; modalTitle='All Awards - Pending'; modalAwards=@js(App\Models\AllAwardResult::where('status','pending')->with(['sport','event','user'])->get());"
                 class="cursor-pointer bg-yellow-100 rounded-lg p-4 text-center hover:bg-yellow-200 transition">
                <div class="text-sm text-yellow-700 font-semibold">
                    Pending
                </div>
                <div class="text-2xl font-bold text-yellow-800 mt-1">
                    {{ $allAwardsPending }}
                </div>
            </div>

            {{-- Approved --}}
            <div @click="openModal = true; modalTitle='All Awards - Approved'; modalAwards=@js(App\Models\AllAwardResult::where('status','approved')->with(['sport','event','user'])->get());"
                 class="cursor-pointer bg-green-100 rounded-lg p-4 text-center hover:bg-green-200 transition">
                <div class="text-sm text-green-700 font-semibold">
                    Approved
                </div>
                <div class="text-2xl font-bold text-green-800 mt-1">
                    {{ $allAwardsApproved }}
                </div>
            </div>

            {{-- Needs Revision --}}
            <div @click="openModal = true; modalTitle='All Awards - Needs Revision'; modalAwards=@js(App\Models\AllAwardResult::where('status','needs_revision')->with(['sport','event','user'])->get());"
                 class="cursor-pointer bg-red-100 rounded-lg p-4 text-center hover:bg-red-200 transition">
                <div class="text-sm text-red-700 font-semibold">
                    Needs Revision
                </div>
                <div class="text-2xl font-bold text-red-800 mt-1">
                    {{ $allAwardsRevision }}
                </div>
            </div>

        </div>

    </div>


    {{-- INDIVIDUAL AWARDS STATUS --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow p-6">

        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Individual Awards Status
        </h2>

        <div class="grid grid-cols-3 gap-4">

            {{-- Pending --}}
            <div @click="openModal = true; modalTitle='Individual Awards - Pending'; modalAwards=@js(App\Models\IndividualAwardResult::where('status','pending')->with(['sport','event','user'])->get());"
                 class="cursor-pointer bg-yellow-100 rounded-lg p-4 text-center hover:bg-yellow-200 transition">
                <div class="text-sm text-yellow-700 font-semibold">
                    Pending
                </div>
                <div class="text-2xl font-bold text-yellow-800 mt-1">
                    {{ $individualAwardsPending }}
                </div>
            </div>

            {{-- Approved --}}
            <div @click="openModal = true; modalTitle='Individual Awards - Approved'; modalAwards=@js(App\Models\IndividualAwardResult::where('status','approved')->with(['sport','event','user'])->get());"
                 class="cursor-pointer bg-green-100 rounded-lg p-4 text-center hover:bg-green-200 transition">
                <div class="text-sm text-green-700 font-semibold">
                    Approved
                </div>
                <div class="text-2xl font-bold text-green-800 mt-1">
                    {{ $individualAwardsApproved }}
                </div>
            </div>

            {{-- Needs Revision --}}
            <div @click="openModal = true; modalTitle='Individual Awards - Needs Revision'; modalAwards=@js(App\Models\IndividualAwardResult::where('status','needs_revision')->with(['sport','event','user'])->get());"
                 class="cursor-pointer bg-red-100 rounded-lg p-4 text-center hover:bg-red-200 transition">
                <div class="text-sm text-red-700 font-semibold">
                    Needs Revision
                </div>
                <div class="text-2xl font-bold text-red-800 mt-1">
                    {{ $individualAwardsRevision }}
                </div>
            </div>

        </div>

    </div>

    {{-- ================= MODAL ================= --}}
    <div x-show="openModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="openModal = false"></div>

        <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full p-6 z-50 overflow-y-auto max-h-[80vh]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800" x-text="modalTitle"></h2>
                <button @click="openModal = false" class="text-gray-500 hover:text-gray-800 font-bold">&times;</button>
            </div>

            <table class="min-w-full table-auto border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left text-sm font-semibold text-gray-700">Athlete / User</th>
                        <th class="border px-3 py-2 text-left text-sm font-semibold text-gray-700">Sport</th>
                        <th class="border px-3 py-2 text-left text-sm font-semibold text-gray-700">Event</th>
                        <th class="border px-3 py-2 text-left text-sm font-semibold text-gray-700">Medal</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="award in modalAwards" :key="award.id">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2 text-sm text-gray-800" x-text="award.user.name"></td>
                            <td class="border px-3 py-2 text-sm text-gray-800" x-text="award.sport.name"></td>
                            <td class="border px-3 py-2 text-sm text-gray-800" x-text="award.event.name"></td>
                            <td class="border px-3 py-2 text-sm text-gray-800 font-semibold" x-text="award.medal"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ============================================================= --}}
{{-- ⏳ TOURNAMENT SECRETARIES WITH FORWARDED AWARDS (PENDING) --}}
{{-- ============================================================= --}}

@if(auth()->user()->role->name === 'Super Admin')

<div class="bg-blue-50 border border-blue-200 rounded-xl shadow p-6 mt-8 mb-10">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-blue-800">
            Tournament Secretaries With Pending Award Submissions
        </h2>

        <span class="text-sm text-blue-700 font-semibold">
            Awards forwarded and waiting for approval
        </span>
    </div>

    @if($secretariesPendingAwards->isEmpty())

        <div class="text-gray-500 text-center py-4">
            No pending award submissions from tournament secretaries.
        </div>

    @else

    <div class="overflow-x-auto rounded-lg border border-blue-200">
        <table class="min-w-full bg-white">

            <thead class="bg-blue-100 text-blue-800 text-sm">
                <tr>
                    <th class="px-4 py-3 text-left">Secretary Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Sport</th>
                    <th class="px-4 py-3 text-left">Sport Type</th>
                    <th class="px-4 py-3 text-left">Level</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Pending Awards</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @foreach($secretariesPendingAwards as $secretary)

                <tr class="hover:bg-blue-50 transition">

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $secretary->name }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $secretary->email }}
                    </td>

                    {{-- Sports --}}
                    <td class="px-4 py-3 text-gray-600">
                        @if(!empty($secretary->sports_access))
                            {{ collect($secretary->sports_access)->map(function($id) use ($sports) {
                                return optional($sports->firstWhere('id',$id))->name;
                            })->filter()->implode(', ') }}
                        @else
                            N/A
                        @endif
                    </td>

                    {{-- Sport Types --}}
                    <td class="px-4 py-3 text-gray-600">
                        @if(!empty($secretary->sport_type_access))
                            {{ collect($secretary->sport_type_access)->map(function($id) use ($sportTypes) {
                                return optional($sportTypes->firstWhere('id',$id))->type_name;
                            })->filter()->implode(', ') }}
                        @else
                            N/A
                        @endif
                    </td>

                    {{-- Level --}}
                    <td class="px-4 py-3 text-gray-600">
                        {{ implode(', ', $secretary->level_access ?? []) ?: 'N/A' }}
                    </td>

                    {{-- Category --}}
                    <td class="px-4 py-3 text-gray-600">
                        {{ implode(', ', $secretary->category_access ?? []) ?: 'N/A' }}
                    </td>

                    {{-- Pending Awards Count --}}
                    <td class="px-4 py-3 font-semibold text-blue-700">
                        {{ $secretary->all_awards_pending_count + $secretary->individual_awards_pending_count }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>
    </div>

    @endif

</div>

@endif

{{-- ============================================================= --}}
{{-- ⚠️ TOURNAMENT SECRETARIES WITH AWARDS NEEDING REVISION --}}
{{-- ============================================================= --}}
@php
$isSuperAdmin = auth()->user()?->role?->name === 'Super Admin';
@endphp

@if($isSuperAdmin)

<div class="mt-8 bg-red-50 border border-red-200 rounded-xl shadow p-6 mb-10">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-red-800">
            Tournament Secretaries with Awards Needing Revision
        </h2>

        <span class="text-sm text-red-600 font-semibold">
            Monitor and contact responsible secretaries
        </span>
    </div>

    @if($secretaryRevisionList->isEmpty())

        <div class="text-gray-500 text-center py-4">
            No tournament secretaries currently have awards needing revision.
        </div>

    @else

    <div class="overflow-x-auto rounded-lg border border-red-200">
        <table class="min-w-full bg-white">

            <thead class="bg-red-100 text-red-800 text-sm">
                <tr>
                    <th class="px-4 py-3 text-left">Secretary Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-center">All Awards Revision</th>
                    <th class="px-4 py-3 text-center">Individual Awards Revision</th>
                    <th class="px-4 py-3 text-center">Total</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @foreach($secretaryRevisionList as $secretary)

                @php
                $total = $secretary->all_awards_revision_count + $secretary->individual_awards_revision_count;
                @endphp

                <tr class="hover:bg-red-50 transition">

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $secretary->name }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $secretary->email }}
                    </td>

                    <td class="px-4 py-3 text-center text-red-600 font-bold">
                        {{ $secretary->all_awards_revision_count }}
                    </td>

                    <td class="px-4 py-3 text-center text-red-600 font-bold">
                        {{ $secretary->individual_awards_revision_count }}
                    </td>

                    <td class="px-4 py-3 text-center font-bold text-red-700">
                        {{ $total }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>
    </div>

    @endif

</div>

@endif

{{-- ============================================================= --}}
{{-- ⏳ TOURNAMENT SECRETARIES WHO HAVEN'T SUBMITTED AWARDS --}}
{{-- ============================================================= --}}

@if($isSuperAdmin)

<div class="bg-yellow-50 border border-yellow-200 rounded-xl shadow p-6 mt-8 mb-10">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-yellow-800">
            Tournament Secretaries Without Award Submissions
        </h2>

        <span class="text-sm text-yellow-700 font-semibold">
            These secretaries have not submitted any awards yet
        </span>
    </div>

    @if($secretariesNoAwards->isEmpty())

        <div class="text-gray-500 text-center py-4">
            All tournament secretaries have already submitted awards.
        </div>

    @else

    <div class="overflow-x-auto rounded-lg border border-yellow-200">
        <table class="min-w-full bg-white">

            <thead class="bg-yellow-100 text-yellow-800 text-sm">
                <tr>
                    <th class="px-4 py-3 text-left">Secretary Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Sport</th>
                    <th class="px-4 py-3 text-left">Sport Type</th>
                    <th class="px-4 py-3 text-left">Level</th>
                    <th class="px-4 py-3 text-left">Category</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @foreach($secretariesNoAwards as $secretary)

                <tr class="hover:bg-yellow-50 transition">

                    <td class="px-4 py-3 font-semibold text-gray-800">
                        {{ $secretary->name }}
                    </td>

                    <td class="px-4 py-3 text-gray-600">
                        {{ $secretary->email }}
                    </td>

                    {{-- Sports Access --}}
                    <td class="px-4 py-3 text-gray-600">
                        @if(!empty($secretary->sports_access))
                            {{ collect($secretary->sports_access)->map(function($id) use ($sports) {
                                return optional($sports->firstWhere('id', $id))->name;
                            })->filter()->implode(', ') }}
                        @else
                            N/A
                        @endif
                        </td>

                    {{-- Sport Type Access --}}
                    <td class="px-4 py-3 text-gray-600">
@if(!empty($secretary->sport_type_access))
    {{ collect($secretary->sport_type_access)->map(function($id) use ($sportTypes) {
        return optional($sportTypes->firstWhere('id', $id))->type_name;
    })->filter()->implode(', ') }}
@else
    N/A
@endif
</td>

                    {{-- Level Access --}}
                    <td class="px-4 py-3 text-gray-600">
                        {{ implode(', ', $secretary->level_access ?? []) ?: 'N/A' }}
                    </td>

                    {{-- Category Access --}}
                    <td class="px-4 py-3 text-gray-600">
                        {{ implode(', ', $secretary->category_access ?? []) ?: 'N/A' }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>
    </div>

    @endif

</div>

@endif

{{-- ================= EVENT STATUS GRAPH ================= --}}
<div class="mb-10 bg-white border border-gray-200 rounded-xl shadow p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">
            Event Status Overview
        </h2>
        <p class="text-sm text-gray-500 mt-1 md:mt-0">
            Distribution of events by current status
        </p>
    </div>

    <div class="relative w-full h-[320px]">
        <canvas id="eventStatusChart"></canvas>
    </div>
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


{{-- ================= EVENT STATUS CHART SCRIPT ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('eventStatusChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar', // 👉 change to 'doughnut' or 'pie' if you want
        data: {
            labels: ['Upcoming', 'Ongoing', 'Finished', 'Delayed'],
            datasets: [{
                label: 'Number of Events',
                data: [
                    {{ $eventStatusCounts['Upcoming'] }},
                    {{ $eventStatusCounts['Ongoing'] }},
                    {{ $eventStatusCounts['Finished'] }},
                    {{ $eventStatusCounts['Delayed'] }}
                ],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',  // Blue
                    'rgba(34, 197, 94, 0.7)',   // Green
                    'rgba(107, 114, 128, 0.7)', // Gray
                    'rgba(239, 68, 68, 0.7)'    // Red
                ],
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#111827',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#e5e7eb'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>


@endsection
