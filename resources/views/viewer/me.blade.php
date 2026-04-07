{{-- 
    Modern & Simple Monitoring & Evaluation Dashboard
    - Clean card design with subtle shadows
    - Gradient accents for better visual appeal
    - Improved typography and spacing
    - Responsive grid layouts
--}}
@extends('layouts.viewer')

@section('page-title', 'Monitoring & Evaluation')

@section('content')

@php
    $meQrs = \App\Models\LayoutSetting::where('key', 'me_qr_settings')->first();
    $meQrs = $meQrs ? json_decode($meQrs->value, true) : [];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="text-center py-6">
        <div class="inline-flex items-center justify-center p-2 bg-blue-50 rounded-2xl mb-4">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
            Monitoring & Evaluation
        </h1>
        <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
            Track performance, progress, and system insights in real-time.
        </p>
    </div>

    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Card 1 --}}
        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Athletes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Athlete::count() }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-400">Registered athletes</div>
        </div>

        {{-- Card 2 --}}
        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Event::count() }}</p>
                </div>
                <div class="h-12 w-12 bg-emerald-50 rounded-xl flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-400">Total scheduled events</div>
        </div>

        {{-- Card 3 --}}
        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completed Events</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Event::where('status', 'Finished and Awarded')->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-50 rounded-xl flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-400">Finished & awarded</div>
        </div>

        {{-- Card 4 --}}
        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">
                        @php
                            $total = \App\Models\Event::count();
                            $completed = \App\Models\Event::where('status', 'Finished and Awarded')->count();
                            $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
                        @endphp
                        {{ $rate }}%
                    </p>
                </div>
                <div class="h-12 w-12 bg-amber-50 rounded-xl flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-400">Overall event completion</div>
        </div>
    </div>

    {{-- ================= M&E QR SECTION ================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">M&E QR Codes</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Scan to access monitoring forms and reports</p>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">{{ count($meQrs) }} items</span>
            </div>
        </div>

        <div class="p-6">
            @if(count($meQrs))
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-5">
                    @foreach($meQrs as $qr)
                        <div class="group bg-gray-50 rounded-xl p-4 text-center hover:bg-white hover:shadow-md transition-all duration-200 border border-transparent hover:border-gray-100">
                            {{-- QR IMAGE --}}
                            <div class="flex justify-center mb-3">
                                <div class="p-2 bg-white rounded-xl shadow-sm">
                                    <img src="{{ isset($qr['image']) ? asset('storage/' . $qr['image']) : 'https://via.placeholder.com/100?text=QR' }}"
                                         class="h-24 w-24 object-contain">
                                </div>
                            </div>

                            {{-- NAME --}}
                            <p class="font-medium text-gray-800 text-sm mb-2 truncate">
                                {{ $qr['name'] ?? 'Unnamed' }}
                            </p>

                            {{-- LINK --}}
                            @if(!empty($qr['link']))
                                <a href="{{ $qr['link'] }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium">
                                    Open Link
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            @else
                                <span class="text-xs text-gray-400">No link available</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500">No QR codes added yet.</p>
                    <p class="text-sm text-gray-400 mt-1">Configure M&E QR settings to display monitoring links.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ================= RECENT EVENTS ================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Recent Events</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Latest 10 events and their current status</p>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">Live updates</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Event</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Division</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Category</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse(\App\Models\Event::latest()->take(10)->get() as $event)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $event->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $event->age_group ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $event->category ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($event->status === 'Finished and Awarded') bg-emerald-50 text-emerald-700
                                    @elseif($event->status === 'Ongoing') bg-blue-50 text-blue-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        @if($event->status === 'Finished and Awarded') bg-emerald-500
                                        @elseif($event->status === 'Ongoing') bg-blue-500 animate-pulse
                                        @else bg-gray-400
                                        @endif"></span>
                                    {{ $event->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                No events available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= SYSTEM INFO ================= --}}
    <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-blue-50 rounded-xl">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">System Insights</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Real-time monitoring of athlete participation and attendance</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Track awarded and ongoing events</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Centralized data for SDO performance evaluation</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Supports decision-making for tournament officials</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection