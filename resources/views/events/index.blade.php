@extends('layouts.app')

@section('page-title', 'Events Management')

@section('content')
<div class="space-y-6">
    {{-- ================= HEADER WITH GRADIENT ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Events Management</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Manage competitions, schedules, and match outcomes</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('events.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Event
        </a>
    </div>

    {{-- ================= INSTRUCTIONS CARD ================= --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50/50 border border-blue-100 rounded-2xl p-5 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900 mb-2">Quick Guide</h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-1 text-sm text-blue-800/80">
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        View all registered competitions
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Use search to quickly find events
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Update event status with reason if delayed
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Click Manage to handle matches
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ================= FILTERS & STATS ================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form method="GET" action="{{ route('events.index') }}" class="flex flex-wrap items-center gap-3">
                {{-- Search --}}
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search events, sport, venue..."
                           class="w-full sm:w-80 pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Status Filter --}}
                <div class="relative">
                    <select name="status" class="w-full sm:w-48 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all appearance-none bg-white">
                        <option value="">All Status</option>
                        @foreach(['Upcoming','Ongoing','Delayed','Finished'] as $s)
                            <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    Filter
                </button>

                <a href="{{ route('events.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                    Reset
                </a>
            </form>

            {{-- Count Badge --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="text-sm text-gray-600">
                    @if(request('status'))
                        <span class="font-semibold">{{ request('status') }} Events:</span>
                    @elseif(request('search'))
                        <span class="font-semibold">Filtered:</span>
                    @else
                        <span class="font-semibold">Total Events:</span>
                    @endif
                    <span class="ml-1 font-bold text-gray-900">{{ $displayCount ?? $totalEvents }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Events Table --}}
    @if($events->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No events found</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first event</p>
            <a href="{{ route('events.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Event
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matches</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Winners</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Status --}}
                                <td class="px-6 py-4" x-data="{
                                    status: '{{ $event->status }}',
                                    reason: '{{ $event->status_reason ?? '' }}',
                                    submitForm() { $refs.statusForm.submit(); }
                                }">
                                    <form method="POST" action="{{ route('events.updateStatus', $event->id) }}" x-ref="statusForm">
                                        @csrf
                                        <div class="flex items-center gap-2">
                                            <select name="status" x-model="status" @change="submitForm()"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-medium border-0 focus:ring-2 focus:ring-offset-2
                                                        @php
                                                            echo match($event->status) {
                                                                'Upcoming' => 'bg-blue-100 text-blue-700 focus:ring-blue-500',
                                                                'Ongoing'  => 'bg-green-100 text-green-700 focus:ring-green-500',
                                                                'Delayed'  => 'bg-yellow-100 text-yellow-700 focus:ring-yellow-500',
                                                                'Finished' => 'bg-gray-200 text-gray-700 focus:ring-gray-500',
                                                                default    => 'bg-gray-100 text-gray-600',
                                                            };
                                                        @endphp">
                                                @foreach(['Upcoming','Ongoing','Delayed','Finished'] as $s)
                                                    <option value="{{ $s }}" :selected="status === '{{ $s }}'">{{ $s }}</option>
                                                @endforeach
                                            </select>
                                            
                                            <template x-if="status === 'Delayed'">
                                                <input type="text" name="status_reason" x-model="reason" 
                                                       placeholder="Reason for delay..."
                                                       class="w-40 px-2 py-1 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-200"
                                                       @change="submitForm()">
                                            </template>
                                        </div>
                                    </form>
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}
                                    </div>
                                </td>

                                {{-- Sport & Event Name --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $event->sport->name ?? 'TBA' }}</div>
                                </td>

                                {{-- Event Type --}}
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                        {{ $event->type ?? 'Regular' }}
                                    </span>
                                </td>

                                {{-- Level --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $event->age_group ?? 'TBA' }}
                                </td>

                                {{-- Category --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $event->category ?? 'TBA' }}
                                </td>

                                {{-- Matches --}}
                                <td class="px-6 py-4">
                                    @if(!empty($event->games) && is_array($event->games))
                                        <div class="space-y-1">
                                            @foreach($event->games as $game)
                                                <div class="text-xs bg-gray-100 rounded-lg px-2 py-1">
                                                    Game {{ $loop->iteration }}: {{ $game }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">TBA</span>
                                    @endif
                                </td>

                                {{-- Winners --}}
                                <td class="px-6 py-4">
                                    @if(is_array($event->game_winners) && count($event->game_winners))
                                        <div class="space-y-1">
                                            @foreach($event->game_winners as $gameIndex => $divisionId)
                                                @php
                                                    $division = $event->divisions->firstWhere('id', $divisionId);
                                                @endphp
                                                <div class="text-xs bg-emerald-50 text-emerald-700 rounded-lg px-2 py-1">
                                                    Game {{ $gameIndex + 1 }}: {{ $division?->name ?? 'TBA' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">TBA</span>
                                    @endif
                                </td>

                                {{-- Venue --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $event->venue ?? 'TBA' }}
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('events.manage', $event->id) }}"
                                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                           title="Manage">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('events.edit', $event->id) }}"
                                           class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('events.destroy', $event->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this event?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($events->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $events->withQueryString()->links() }}
            </div>
            @endif
        </div>
    @endif
</div>
@endsection