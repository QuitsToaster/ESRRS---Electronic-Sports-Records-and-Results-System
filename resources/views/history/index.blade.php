@extends('layouts.app')

@section('page-title', 'My History')

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="border-b border-gray-200 pb-4">
        <p class="text-sm text-gray-500 mt-1">Review your submitted, approved, and rejected awards</p>
    </div>

    {{-- ================= TABS ================= --}}
    <div x-data="{ tab: 'all' }">

        <div class="flex gap-1 border-b border-gray-200">
            <button @click="tab='all'"
                :class="tab === 'all' ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500'"
                class="py-2.5 px-5 text-sm font-medium border-b-2">
                All Awards
            </button>

            <button @click="tab='individual'"
                :class="tab === 'individual' ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500'"
                class="py-2.5 px-5 text-sm font-medium border-b-2">
                Individual Awards
            </button>
        </div>

        {{-- ================= ALL AWARDS ================= --}}
<div x-show="tab === 'all'" class="mt-6 space-y-4">

    @forelse($allAwards as $award)

    @php
        // ✅ Resolve Event Name SAFELY
        $eventName = 'N/A';

        if (!empty($award->event_id)) {
            $event = \App\Models\Event::find($award->event_id);
            $eventName = $event->name ?? 'N/A';
        }
    @endphp

    <div class="bg-white border rounded-lg p-5 shadow-sm">

        {{-- HEADER --}}
        <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">

                {{-- Medal Icon --}}
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shadow-inner border">
                    @if($award->medal == 'GOLD')
                        <div class="w-6 h-6 bg-yellow-400 rounded-full"></div>
                    @elseif($award->medal == 'SILVER')
                        <div class="w-6 h-6 bg-gray-300 rounded-full"></div>
                    @elseif($award->medal == 'BRONZE')
                        <div class="w-6 h-6 bg-orange-500 rounded-full"></div>
                    @else
                        <div class="w-6 h-6 bg-gray-400 rounded-full"></div>
                    @endif
                </div>

                <div>
                    {{-- ✅ FIXED HERE --}}
                    <h3 class="font-semibold text-lg">
                        {{ $eventName }}
                    </h3>

                    <p class="text-sm text-gray-500">
                        {{ $award->sdo ?? 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- STATUS --}}
            <span class="
                px-3 py-1 text-xs rounded-full font-medium
                @if($award->history_action=='Approved') bg-green-100 text-green-800
                @elseif($award->history_action=='Rejected') bg-red-100 text-red-800
                @elseif($award->history_action=='Submitted') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800
                @endif
            ">
                {{ $award->history_action }}
            </span>
        </div>

        {{-- DETAILS --}}
        <div class="mt-3 text-sm grid grid-cols-2 gap-2">
            <div><b>Medal:</b> {{ $award->medal }}</div>
            <div><b>Date:</b> {{ $award->history_date->format('M d, Y h:i A') }}</div>
        </div>

        {{-- LOG --}}
        <div class="mt-3 text-xs text-gray-500">
            {{ $award->history_log }}
        </div>

    </div>

    @empty
    <div class="text-center text-gray-500 py-10">
        No records found
    </div>
    @endforelse

</div>

        {{-- ================= INDIVIDUAL ================= --}}
        <div x-show="tab === 'individual'" class="mt-6 space-y-4">

            @forelse($individualAwards as $award)
            <div class="bg-white border rounded-lg p-5 shadow-sm">

                {{-- HEADER --}}
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        {{-- Medal SVG Icon --}}
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shadow-inner border">
                    @if($award->medal == 'GOLD')
                        <div class="w-6 h-6 bg-yellow-400 rounded-full"></div>
                    @elseif($award->medal == 'SILVER')
                        <div class="w-6 h-6 bg-gray-300 rounded-full"></div>
                    @elseif($award->medal == 'BRONZE')
                        <div class="w-6 h-6 bg-orange-500 rounded-full"></div>
                    @else
                        <div class="w-6 h-6 bg-gray-400 rounded-full"></div>
                    @endif
                </div>
                        <div>
                            <h3 class="font-semibold text-lg">
                                {{ $award->athlete ?? 'Unknown Athlete' }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $award->sport ?? 'N/A' }} • {{ $award->type ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    {{-- ✅ USE history_action --}}
                    <span class="
                        px-3 py-1 text-xs rounded-full font-medium
                        @if($award->history_action=='Approved') bg-green-100 text-green-800
                        @elseif($award->history_action=='Rejected') bg-red-100 text-red-800
                        @elseif($award->history_action=='Submitted') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ $award->history_action }}
                    </span>
                </div>

                {{-- DETAILS --}}
                <div class="mt-3 text-sm grid grid-cols-2 gap-2">
                    <div><b>Medal:</b> {{ $award->medal }}</div>
                    <div><b>SDO:</b> {{ $award->sdo }}</div>
                    <div><b>Date:</b> {{ $award->history_date->format('M d, Y h:i A') }}</div>
                </div>

                {{-- LOG --}}
                <div class="mt-3 text-xs text-gray-500">
                    {{ $award->history_log }}
                </div>

            </div>
            @empty
            <div class="text-center text-gray-500 py-10">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L15 8.5L22 9.5L17 14L18.5 21L12 17.5L5.5 21L7 14L2 9.5L9 8.5L12 2Z" fill="#E5E7EB" stroke="#9CA3AF" stroke-width="1.2" stroke-linejoin="round"/>
                </svg>
                No individual awards
            </div>
            @endforelse

        </div>

    </div>

</div>

@endsection