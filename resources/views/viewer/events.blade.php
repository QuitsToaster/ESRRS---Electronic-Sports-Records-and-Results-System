@extends('layouts.viewer')

@section('page-title', 'Events')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white">

    <div class="max-w-7xl mx-auto px-6 py-14 space-y-14">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">

            <div>
                <h1 class="text-5xl font-bold tracking-tight" style="font-family: 'Poppins'">
                    Don't miss the action.
                </h1>
                <p class="text-gray-400 mt-3 text-lg" style="font-family: 'Poppins'">
                    Stay updated with competitions, schedules, and results.
                </p>
            </div>

            {{-- SEARCH & FILTER --}}
            <form method="GET" action="{{ route('viewer.events') }}"
                  class="flex flex-wrap gap-3 items-center">

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search events..."
                       class="bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none transition w-56">

                <select name="status"
                        class="bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-red-500 outline-none transition">
                    <option value="">All Status</option>
                    @foreach(['Upcoming','Ongoing','Finished','Delayed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>

                <button class="bg-red-600 hover:bg-red-700 px-5 py-2.5 rounded-xl font-semibold transition">
                    Search
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('viewer.events') }}"
                       class="text-gray-400 hover:text-white text-sm">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ================= EVENT COUNT ================= --}}
<div class="grid grid-cols-1 gap-6">

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl">
        <div class="text-sm text-gray-400 uppercase tracking-wide">
            {{ $eventsLabel }}
        </div>

        <div class="mt-2 text-4xl font-bold text-white">
            {{ $eventsCount }}
        </div>

        <div class="mt-1 text-xs text-gray-500">
            {{ $eventsLabel === 'Total Events'
                ? 'All events in the system'
                : 'Based on current filters' }}
        </div>
    </div>

</div>

    {{-- ================= EVENTS DISPLAY ================= --}}
@if($events->isEmpty())

    <div class="text-center text-gray-500 py-24 text-lg">
        No events available at the moment.
    </div>

@else

@php
    // GROUP BY SPORT
    $groupedEvents = $events->groupBy(function ($event) {
        return $event->sport->name ?? 'Other Events';
    });

    // pagination size
    $perPage = 6;
@endphp

<div x-data="eventsPage()" class="space-y-14">

@foreach($groupedEvents as $sportName => $sportEvents)

    @php
    $sportKey = \Illuminate\Support\Str::slug($sportName);

    $currentPage = request($sportKey . '_page', 1);

    $chunks = $sportEvents->values()->chunk($perPage);

    $currentChunk = $chunks->get($currentPage - 1, collect());

    $divisionGroups = $currentChunk->groupBy('age_group');
@endphp

    {{-- ================= SPORT HEADER ================= --}}
    <div>
        <h2 class="text-3xl font-bold text-white mb-6 border-l-4 border-red-500 pl-4">
            {{ $sportName }}
        </h2>

        {{-- ================= DIVISION ================= --}}
        @foreach(['Elementary','Secondary','Paragames'] as $division)

            @if(isset($divisionGroups[$division]))

                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-300 mb-4">
                        {{ $division }}
                    </h3>

                    @php
                        $categoryGroups = $divisionGroups[$division]->groupBy('category');
                    @endphp

                    {{-- ================= CATEGORY ================= --}}
                    @foreach(['Boys','Girls','Mixed'] as $category)

                        @if(isset($categoryGroups[$category]))

                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-400 mb-4 pl-2 border-l-2 border-gray-600">
                                    {{ $category }}
                                </h4>

                                {{-- ================= EVENTS GRID ================= --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

                                    @foreach($categoryGroups[$category] as $event)

                                        @php
                                            $statusColor = match($event->status) {
                                                'Upcoming' => 'bg-green-500/20 text-green-400',
                                                'Ongoing'  => 'bg-blue-500/20 text-blue-400',
                                                'Finished' => 'bg-green-500/20 text-gray-300',
                                                'Delayed'  => 'bg-red-500/20 text-red-400',
                                                default    => 'bg-gray-700 text-gray-300',
                                            };
                                        @endphp

                                        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-7 hover:border-red-500/40 transition duration-300 shadow-lg hover:shadow-red-500/10">

                                            <div class="flex justify-between items-start mb-4">
                                                <h3 class="text-2xl font-semibold leading-tight group-hover:text-red-400 transition">
                                                    {{ $event->custom_name ?: $event->name }}
                                                </h3>

                                                <span class="px-3 py-1 text-xs rounded-full font-medium {{ $statusColor }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                            </div>

                                            <div class="flex flex-wrap gap-2 mb-4 text-xs">
                                                <span class="bg-gray-800 px-3 py-1 rounded-full">
                                                    {{ $event->age_group }}
                                                </span>
                                                <span class="bg-gray-800 px-3 py-1 rounded-full">
                                                    {{ $event->category }}
                                                </span>
                                            </div>

                                            <div class="space-y-2 text-sm text-gray-400">

                                                <div>
                                                    <span class="text-gray-500">Date:</span>
                                                    {{ $event->start_date?->format('M d, Y') ?? 'TBA' }}
                                                </div>

                                                <div>
                                                    <span class="text-gray-500">Time:</span>
                                                    {{ $event->event_time
                                                        ? \Carbon\Carbon::parse($event->event_time)
                                                            ->timezone('Asia/Manila')
                                                            ->format('h:i A')
                                                        : 'TBA' }}
                                                </div>

                                                <div>
                                                    <span class="text-gray-500">Venue:</span>
                                                    {{ $event->venue ?? 'TBA' }}
                                                </div>

                                            </div>

                                            @if(strtolower($event->status) === 'delayed' && $event->status_reason)
                                                <div class="mt-3 text-xs text-red-400 italic">
                                                    Reason: {{ $event->status_reason }}
                                                </div>
                                            @endif

                                            <button
                                                @click="activeEvent = {{ $event->id }}"
                                                class="mt-6 w-full bg-red-600 hover:bg-red-700 py-2.5 rounded-xl font-semibold transition">
                                                View Details
                                            </button>

                                        </div>

                                    @endforeach

                                </div>
                            </div>

                        @endif

                    @endforeach

                </div>

            @endif

        @endforeach

        {{-- ================= PAGINATION ================= --}}
        @if($chunks->count() > 1)
            <div class="flex justify-center mt-6 gap-2">

                @for($i = 1; $i <= $chunks->count(); $i++)
                    <a href="{{ request()->fullUrlWithQuery([$sportKey . '_page' => $i]) }}"
                       class="px-3 py-1 rounded-lg text-sm
                       {{ $i == $currentPage
                            ? 'bg-red-600 text-white'
                            : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        {{ $i }}
                    </a>
                @endfor

            </div>
        @endif

    </div>

@endforeach

{{-- ================= MODALS ================= --}}
<template x-for="event in {{ $eventsJson->toJson() }}" :key="event.id">
    <div x-show="activeEvent === event.id"
         x-transition.opacity
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
         style="display:none">

        <div @click.away="activeEvent = null"
             class="bg-gray-950 border border-white/10 w-full max-w-4xl rounded-3xl shadow-2xl flex flex-col max-h-[90vh]">

            <div class="p-6 border-b border-white/10 flex justify-between items-start shrink-0">
                <div>
                    <h3 class="text-3xl font-bold"
                        x-text="event.custom_name ? event.custom_name : event.name"></h3>
                    <p class="text-gray-400 mt-1 text-sm"
                       x-text="event.category + ' • ' + event.age_group"></p>
                </div>
                <button @click="activeEvent = null"
                        class="text-gray-500 hover:text-white text-3xl leading-none">&times;</button>
            </div>

            <div class="p-8 space-y-8 overflow-y-auto">

                <div class="grid md:grid-cols-3 gap-6 text-sm bg-white/5 rounded-2xl p-6 border border-white/10">
                    <div>
                        <div class="text-gray-400 text-xs uppercase mb-1">Sport</div>
                        <div x-text="event.sport?.name ?? '-'"></div>
                    </div>
                    <div>
                        <div class="text-gray-400 text-xs uppercase mb-1">Venue</div>
                        <div x-text="event.venue ?? 'TBA'"></div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>

</div>

@endif
    </div>
</div>

{{-- ================= ALPINE INIT ================= --}}
<script src="//unpkg.com/alpinejs" defer></script>
<script>
function eventsPage() {
    return {
        activeEvent: null,
        init() {
            const params = new URLSearchParams(window.location.search);
            const eventId = params.get('event');
            if (eventId) {
                this.activeEvent = parseInt(eventId);
            }
        }
    }
}
</script>

@endsection