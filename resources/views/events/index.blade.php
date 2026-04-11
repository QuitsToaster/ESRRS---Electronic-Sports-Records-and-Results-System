@extends('layouts.app')

@section('page-title', 'Events Management')

@section('content')
<div x-data="winnerModal()" x-init="init()" class="space-y-6">
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
        
       @if(auth()->user()->role->name !== 'Awards Committee')
<a href="{{ route('events.create') }}"
   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
    </svg>
    Add Event
</a>
@endif
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

    {{-- ================= BULK CSV UPLOAD ================= --}}
@if(auth()->user()->role->name !== 'Awards Committee')
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">

    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Bulk Upload Schedule</h3>
            <p class="text-sm text-gray-500 mt-1">
                Upload a CSV file to quickly create multiple events.
            </p>
        </div>
    </div>

    {{-- SAMPLE FORMAT --}}
    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4 text-xs text-gray-600">
        <p class="font-semibold mb-2 text-gray-700">Accepted CSV Format:</p>
<pre class="whitespace-pre-wrap">Day,Time,Event,Type of Event,Category,Gender,Venue
April 12, 2026,7:00 AM - 8:00 PM,Basketball,3x3,Secondary,Boys,City Gym
, ,Basketball,5x5,Secondary,Girls,Main Court</pre>

<p class="mt-2 text-gray-500 italic">
    • System is NOT case-sensitive (accepts lowercase, uppercase, mixed)<br>
    • Time accepts ranges (e.g. 7:00 AM - 8:00 PM or 7:00 A.M to 8:00 P.M)<br>
    • Only the FIRST time will be saved as event start time<br>
    • Empty Day/Time will reuse previous row values  
</p>
    </div>

    {{-- FORM --}}
    <form action="{{ route('events.bulkUpload') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
        @csrf

        <div class="flex flex-col sm:flex-row gap-3 items-center">
            <input type="file"
                   name="csv_file"
                   accept=".csv"
                   required
                   class="w-full sm:w-auto border border-gray-300 rounded-xl px-3 py-2 text-sm file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">

            <button type="submit"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition">
                Upload CSV
            </button>
        </div>

        @error('csv_file')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
    </form>
</div>
@endif

    {{-- ================= AWARDS COMMITTEE COUNT ================= --}}
@if(auth()->user()->role->name === 'Awards Committee')

@php
    $pendingAwardCount = $events->filter(function($e) {
        return $e->status === 'Finished';
    })->count();

    $awardedCount = $events->filter(function($e) {
        return $e->status === 'Finished and Awarded';
    })->count();
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

    {{-- 🔵 Pending Awards --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 flex items-center justify-between shadow-sm">
        <div>
            <p class="text-sm text-yellow-700">Events need to be awarded</p>
            <h2 class="text-2xl font-bold text-yellow-900">
                {{ $pendingAwardCount }}
            </h2>
        </div>

        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    {{-- 🟣 Awarded --}}
    <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 flex items-center justify-between shadow-sm">
        <div>
            <p class="text-sm text-purple-700">Awarded Events</p>
            <h2 class="text-2xl font-bold text-purple-900">
                {{ $awardedCount }}
            </h2>
        </div>

        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </div>

</div>

@endif

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
                        @foreach(['Upcoming','Ongoing','Delayed','Finished', 'Finished and Awarded'] as $s)
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

   {{-- Modern Events Grid --}}
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
    @php
        $filteredEvents = $events;
        if(auth()->user()->role->name === 'Awards Committee') {
            $filteredEvents = $events->filter(function($e) {
                return in_array($e->status, ['Finished', 'Finished and Awarded']);
            });
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($filteredEvents as $event)
            <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                {{-- Card Header with Status --}}
                <div class="px-5 pt-5 pb-3 border-b border-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
    <button 
        onclick="showEventNameModal('{{ addslashes($event->name) }}')"
        class="text-left w-full group focus:outline-none"
        title="Click to view full event name"
    >
        <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1 group-hover:text-blue-600 transition-colors duration-200">
    {{-- ✅ SHOW CUSTOM NAME IF EXISTS --}}
    {{ $event->custom_name ?: $event->name }}

    <svg class="inline-block w-4 h-4 ml-1 text-gray-400 group-hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
    </svg>
</h3>
    </button>
    <p class="text-sm text-gray-500">{{ $event->sport->name ?? 'Sport TBA' }}</p>
</div>

<!-- Modern Modal for viewing full event name -->
<div id="eventNameModal" class="fixed inset-0 bg-black/40 backdrop-blur-md flex items-center justify-center z-50 hidden transition-all duration-300" onclick="closeEventNameModal()">
    <div class="relative max-w-lg w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="eventNameModalContent" onclick="event.stopPropagation()">
        <!-- Glass morphism card -->
        <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 overflow-hidden">
            <!-- Animated gradient header -->
            <div class="relative bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 animate-pulse"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">Event Details</h3>
                    </div>
                    <button onclick="closeEventNameModal()" class="text-white/80 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal body with modern design -->
            <div class="p-6">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl p-5 border border-gray-200/50">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Event Name</p>
                            <p class="text-gray-800 text-base leading-relaxed break-words font-medium" id="modalEventName"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Decorative floating elements -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-400 rounded-full blur-3xl opacity-20 animate-pulse pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-400 rounded-full blur-3xl opacity-20 animate-pulse pointer-events-none"></div>
    </div>
</div>

<script>
    let currentEventName = '';
    
    function showEventNameModal(eventName) {
        const modal = document.getElementById('eventNameModal');
        const modalContent = document.getElementById('eventNameModalContent');
        const modalEventName = document.getElementById('modalEventName');
        
        currentEventName = eventName;
        modalEventName.textContent = eventName;
        modal.classList.remove('hidden');
        
        // Prevent body scroll when modal is open
        document.body.style.overflow = 'hidden';
        
        // Trigger animation
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeEventNameModal() {
        const modal = document.getElementById('eventNameModal');
        const modalContent = document.getElementById('eventNameModalContent');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }
    
    function copyEventNameToClipboard() {
        if (currentEventName) {
            navigator.clipboard.writeText(currentEventName).then(() => {
                // Show temporary success feedback
                const copyBtn = event.target.closest('button');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Copied!
                `;
                copyBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                copyBtn.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    copyBtn.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('eventNameModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeEventNameModal();
            }
        }
    });
    
    // Optional: Add touch/swipe support for mobile
    let touchStartY = 0;
    document.getElementById('eventNameModal')?.addEventListener('touchstart', (e) => {
        touchStartY = e.touches[0].clientY;
    });
    
    document.getElementById('eventNameModal')?.addEventListener('touchmove', (e) => {
        const touchEndY = e.touches[0].clientY;
        const diff = touchEndY - touchStartY;
        if (diff > 50) {
            closeEventNameModal();
        }
    });
</script>
                        
                        {{-- Status Badge --}}
                        @if(auth()->user()->role->name === 'Awards Committee')
                            <span class="px-3 py-1 rounded-lg text-xs font-medium shrink-0 ml-2
                                @php
                                    echo match($event->status) {
                                        'Upcoming' => 'bg-blue-100 text-blue-700',
                                        'Ongoing'  => 'bg-green-100 text-green-700',
                                        'Delayed'  => 'bg-yellow-100 text-yellow-700',
                                        'Finished' => 'bg-gray-200 text-gray-700',
                                        'Finished and Awarded' => 'bg-purple-500 text-white',
                                        default    => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp">
                                {{ $event->status }}
                            </span>
                        @else
                            <div x-data="{
                                status: '{{ $event->status }}',
                                reason: '{{ $event->status_reason ?? '' }}',
                                submitForm() { $refs.statusForm.submit(); }
                            }" class="shrink-0 ml-2">
                                <form method="POST" action="{{ route('events.updateStatus', $event->id) }}" x-ref="statusForm">
                                    @csrf
                                    <select name="status" x-model="status" @change="submitForm()"
                                        class="px-3 py-1 rounded-lg text-xs font-medium border-0 focus:ring-2 focus:ring-offset-2 cursor-pointer
                                            @php
                                                echo match($event->status) {
                                                    'Upcoming' => 'bg-blue-100 text-blue-700 focus:ring-blue-500',
                                                    'Ongoing'  => 'bg-green-100 text-green-700 focus:ring-green-500',
                                                    'Delayed'  => 'bg-yellow-100 text-yellow-700 focus:ring-yellow-500',
                                                    'Finished' => 'bg-gray-200 text-gray-700 focus:ring-gray-500',
                                                    'Finished and Awarded' => 'bg-purple-500 text-white',
                                                    default    => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp">
                                        @foreach(['Upcoming','Ongoing','Delayed','Finished','Finished and Awarded'] as $s)
                                            <option value="{{ $s }}" :selected="status === '{{ $s }}'">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <template x-if="status === 'Delayed'">
                                        <input type="text" name="status_reason" x-model="reason" 
                                               placeholder="Reason..."
                                               class="mt-2 w-full px-2 py-1 text-xs border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-200"
                                               @change="submitForm()">
                                    </template>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Delay reason display --}}
                    @if($event->status === 'Delayed' && $event->status_reason && auth()->user()->role->name === 'Awards Committee')
                        <p class="text-xs text-yellow-600 mt-2">Reason: {{ $event->status_reason }}</p>
                    @endif
                </div>
                
                {{-- Card Body --}}
                <div class="p-5 space-y-4">
                    {{-- Date, Time & Venue row --}}
<div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">

    {{-- DATE --}}
    <div class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>
            {{ $event->start_date ? $event->start_date->format('M d, Y') : 'TBA' }}
        </span>
    </div>

    {{-- TIME (PH TIME) --}}
    <div class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
        </svg>
        <span>
            {{ $event->event_time 
                ? \Carbon\Carbon::parse($event->event_time)
                    ->timezone('Asia/Manila')
                    ->format('h:i A') 
                : 'TBA' }}
        </span>
    </div>

    {{-- VENUE --}}
    <div class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span>{{ $event->venue ?? 'TBA' }}</span>
    </div>

</div>
                    
                    {{-- Type, Level, Category chips --}}
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                            {{ $event->type ?? 'Regular' }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                            {{ $event->age_group ?? 'Level TBA' }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                            {{ $event->category ?? 'Category TBA' }}
                        </span>
                    </div>
                    
                    {{-- Matches & Winners Section --}}
<div class="bg-gray-50 rounded-xl p-3 space-y-3">

    {{-- ================= MATCHES / GAMES ================= --}}
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Matches
            </p>

            {{-- ✅ SET WINNERS BUTTON --}}
            @if(auth()->user()->role->name === 'Awards Committee' && !empty($event->games))
                <button type="button"
                        onclick="openWinnerModal({{ $event->id }})"
                        class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">
                    Set Winners
                </button>
            @endif
        </div>

        @if(!empty($event->games) && is_array($event->games))
            <div class="space-y-1">
                @foreach($event->games as $index => $game)
                    <div class="text-xs bg-white rounded-lg px-2 py-1 text-gray-700">
                        Game {{ $index + 1 }}: {{ $game }}
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-sm text-gray-400 italic">
                No matches scheduled
            </span>
        @endif
    </div>

    {{-- ================= WINNERS ================= --}}
    <div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            Winners
        </p>

        @if(is_array($event->game_winners) && count(array_filter($event->game_winners)))
            <div class="space-y-1">
                @foreach($event->game_winners as $gameIndex => $divisionId)
                    @php
                        $division = $event->divisions->firstWhere('id', $divisionId);
                    @endphp

                    <div class="text-xs bg-emerald-50 text-emerald-700 rounded-lg px-2 py-1">
                        Game {{ $gameIndex + 1 }}:
                        {{ $division?->name ?? 'TBA' }}
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-sm text-gray-400 italic">
                No winners yet
            </span>
        @endif
    </div>

</div>


{{-- ================= SIMPLE MODAL ================= --}}
<div id="winnerModal-{{ $event->id }}"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Set Winners</h2>
            <button onclick="closeWinnerModal({{ $event->id }})"
                    class="text-gray-500 text-xl">&times;</button>
        </div>

        <form method="POST" action="{{ route('events.saveGameWinners', $event->id) }}">
            @csrf

            @foreach($event->games ?? [] as $index => $game)
                @php
                    $existing = $event->game_winners[$index] ?? '';

                    // Extract participants
                    $participants = collect(explode(' vs ', $game))
                        ->map(fn($p) => trim($p));

                    // Filter divisions
                    $filteredDivisions = $event->divisions->filter(function($division) use ($participants) {
                        return $participants->contains($division->name);
                    });
                @endphp

                <div class="mb-3">
                    <label class="text-sm font-medium">
                        Game {{ $index + 1 }}
                    </label>

                    {{-- ✅ IF LOCKED --}}
                    @if($existing)
                        {{-- Preserve value --}}
                        <input type="hidden"
                               name="game_winners[{{ $index }}]"
                               value="{{ $existing }}">

                        {{-- Disabled dropdown --}}
                        <select disabled
                                class="w-full border rounded px-3 py-2 mt-1 bg-gray-200 text-gray-500 cursor-not-allowed">

                            @foreach($filteredDivisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ $existing == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach

                        </select>

                        <p class="text-xs text-emerald-600 mt-1">
                            ✔ Winner already set (locked)
                        </p>

                    @else
                        {{-- Editable dropdown --}}
                        <select name="game_winners[{{ $index }}]"
                                class="w-full border rounded px-3 py-2 mt-1">

                            <option value="">-- Select Winner --</option>

                            @foreach($filteredDivisions as $division)
                                <option value="{{ $division->id }}">
                                    {{ $division->name }}
                                </option>
                            @endforeach

                        </select>
                    @endif
                </div>
            @endforeach

            <div class="flex justify-end mt-4">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Save Winners
                </button>
            </div>
        </form>

    </div>
</div>
                </div>
                
                {{-- Card Footer Actions --}}
                <div class="px-5 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-2">
                    @if(auth()->user()->role->name === 'Awards Committee')
                        @if($event->status === 'Finished')
                            <form method="POST" action="{{ route('events.markAwarded', $event->id) }}">
                                @csrf
                                <button type="button"
    onclick="openAwardModal({{ $event->id }})"
    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
    Mark as Awarded
</button>
                            </form>
                        @else
                            <span class="text-sm text-gray-400 italic">Not eligible for awarding</span>
                        @endif
                    @else
                        <a href="{{ route('events.manage', $event->id) }}"
                           class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-xl text-sm font-medium transition">
                            Manage
                        </a>
                        <a href="{{ route('events.edit', $event->id) }}"
                           class="px-4 py-2 text-amber-600 hover:bg-amber-50 rounded-xl text-sm font-medium transition">
                            Edit
                        </a>
                        <form action="{{ route('events.destroy', $event->id) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this event?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-xl text-sm font-medium transition">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No finished events available</h3>
                <p class="text-gray-500">Check back later for completed events</p>
            </div>
        @endforelse
        {{-- ================= AWARD CONFIRM MODAL ================= --}}
<div id="awardModal-{{ $event->id }}"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">

        <h2 class="text-lg font-bold mb-2">Confirm Award</h2>
        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to mark this event as <strong>Awarded</strong>?
        </p>

        <div class="flex justify-end gap-3">
            <button onclick="closeAwardModal({{ $event->id }})"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
                Cancel
            </button>

            <form method="POST" action="{{ route('events.markAwarded', $event->id) }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                    Yes, Confirm
                </button>
            </form>
        </div>

    </div>
</div>
</div>
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

<script>
function openWinnerModal(id) {
    const modal = document.getElementById('winnerModal-' + id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeWinnerModal(id) {
    const modal = document.getElementById('winnerModal-' + id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
<script>
function openAwardModal(id) {
    const modal = document.getElementById('awardModal-' + id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAwardModal(id) {
    const modal = document.getElementById('awardModal-' + id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection