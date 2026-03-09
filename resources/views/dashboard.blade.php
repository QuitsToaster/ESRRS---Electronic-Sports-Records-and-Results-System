@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    {{-- Divisions Card --}}
    <a href="{{ route('divisions.index') }}" class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-blue-200 hover:shadow-xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $divisionsCount }}</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Divisions</h2>
            <p class="text-sm text-gray-500">Total number of participating divisions</p>
        </div>
    </a>

    {{-- Athletes Card --}}
    <a href="{{ route('athletes.index') }}" class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-green-200 hover:shadow-xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $athletesCount }}</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Athletes</h2>
            <p class="text-sm text-gray-500">Total number of registered athletes</p>
        </div>
    </a>

    {{-- Events Card --}}
    <a href="{{ route('events.index') }}" class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-yellow-200 hover:shadow-xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $eventsCount }}</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Events</h2>
            <p class="text-sm text-gray-500">Total number of scheduled events</p>
        </div>
    </a>

    {{-- Sports Card --}}
    <a href="{{ route('sports.index') }}" class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-purple-200 hover:shadow-xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke-width="2" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20M2 12h20" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.9 5.9l12.2 12.2M18.1 5.9L5.9 18.1" />
</svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $sportsCount }}</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Sports</h2>
            <p class="text-sm text-gray-500">Total number of sports categories</p>
        </div>
    </a>

    {{-- Users Card --}}
    <a href="{{ route('users.index') }}" class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-indigo-200 hover:shadow-xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-900">{{ $usersCount }}</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Accounts</h2>
            <p class="text-sm text-gray-500">Total number of system users</p>
        </div>
    </a>
</div>
{{-- ============================================================= --}}
{{-- 🏆 AWARDS STATUS OVERVIEW (Clickable with Modal) --}}
{{-- ============================================================= --}}

<div x-data="{ openModal: false, modalTitle: '', modalAwards: [] }" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    {{-- ALL AWARDS STATUS --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">All Awards Status</h2>
        </div>

        <div class="grid grid-cols-3 gap-3">
            {{-- Pending --}}
            <div @click="openModal = true; modalTitle='All Awards - Pending'; modalAwards=@js(App\Models\AllAwardResult::where('status','pending')->with(['sport','event','user'])->get());"
                 class="group relative bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-xl p-4 cursor-pointer hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    <div class="text-xs font-medium text-amber-700 uppercase tracking-wider mb-1">Pending</div>
                    <div class="text-2xl font-bold text-amber-900">{{ $allAwardsPending }}</div>
                    <div class="absolute top-0 right-0 w-8 h-8 bg-amber-200/50 rounded-full group-hover:scale-150 transition-transform duration-300 opacity-0 group-hover:opacity-100"></div>
                </div>
            </div>

            {{-- Approved --}}
            <div @click="openModal = true; modalTitle='All Awards - Approved'; modalAwards=@js(App\Models\AllAwardResult::where('status','approved')->with(['sport','event','user'])->get());"
                 class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl p-4 cursor-pointer hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    <div class="text-xs font-medium text-emerald-700 uppercase tracking-wider mb-1">Approved</div>
                    <div class="text-2xl font-bold text-emerald-900">{{ $allAwardsApproved }}</div>
                    <div class="absolute top-0 right-0 w-8 h-8 bg-emerald-200/50 rounded-full group-hover:scale-150 transition-transform duration-300 opacity-0 group-hover:opacity-100"></div>
                </div>
            </div>

            {{-- Needs Revision --}}
            <div @click="openModal = true; modalTitle='All Awards - Needs Revision'; modalAwards=@js(App\Models\AllAwardResult::where('status','needs_revision')->with(['sport','event','user'])->get());"
                 class="group relative bg-gradient-to-br from-rose-50 to-rose-100/50 rounded-xl p-4 cursor-pointer hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    <div class="text-xs font-medium text-rose-700 uppercase tracking-wider mb-1">Needs Revision</div>
                    <div class="text-2xl font-bold text-rose-900">{{ $allAwardsRevision }}</div>
                    <div class="absolute top-0 right-0 w-8 h-8 bg-rose-200/50 rounded-full group-hover:scale-150 transition-transform duration-300 opacity-0 group-hover:opacity-100"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- INDIVIDUAL AWARDS STATUS --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Individual Awards Status</h2>
        </div>

        <div class="grid grid-cols-3 gap-3">
            {{-- Pending --}}
            <div @click="openModal = true; modalTitle='Individual Awards - Pending'; modalAwards=@js(App\Models\IndividualAwardResult::where('status','pending')->with(['sport','event','user'])->get());"
                 class="group relative bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-xl p-4 cursor-pointer hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    <div class="text-xs font-medium text-amber-700 uppercase tracking-wider mb-1">Pending</div>
                    <div class="text-2xl font-bold text-amber-900">{{ $individualAwardsPending }}</div>
                    <div class="absolute top-0 right-0 w-8 h-8 bg-amber-200/50 rounded-full group-hover:scale-150 transition-transform duration-300 opacity-0 group-hover:opacity-100"></div>
                </div>
            </div>

            {{-- Approved --}}
            <div @click="openModal = true; modalTitle='Individual Awards - Approved'; modalAwards=@js(App\Models\IndividualAwardResult::where('status','approved')->with(['sport','event','user'])->get());"
                 class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl p-4 cursor-pointer hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    <div class="text-xs font-medium text-emerald-700 uppercase tracking-wider mb-1">Approved</div>
                    <div class="text-2xl font-bold text-emerald-900">{{ $individualAwardsApproved }}</div>
                    <div class="absolute top-0 right-0 w-8 h-8 bg-emerald-200/50 rounded-full group-hover:scale-150 transition-transform duration-300 opacity-0 group-hover:opacity-100"></div>
                </div>
            </div>

            {{-- Needs Revision --}}
            <div @click="openModal = true; modalTitle='Individual Awards - Needs Revision'; modalAwards=@js(App\Models\IndividualAwardResult::where('status','needs_revision')->with(['sport','event','user'])->get());"
                 class="group relative bg-gradient-to-br from-rose-50 to-rose-100/50 rounded-xl p-4 cursor-pointer hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    <div class="text-xs font-medium text-rose-700 uppercase tracking-wider mb-1">Needs Revision</div>
                    <div class="text-2xl font-bold text-rose-900">{{ $individualAwardsRevision }}</div>
                    <div class="absolute top-0 right-0 w-8 h-8 bg-rose-200/50 rounded-full group-hover:scale-150 transition-transform duration-300 opacity-0 group-hover:opacity-100"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="openModal" class="fixed inset-0 flex items-center justify-center z-50" x-cloak>
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="openModal = false"></div>
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-6 z-50 overflow-y-auto max-h-[85vh]">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900" x-text="modalTitle"></h2>
                <button @click="openModal = false" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-colors flex items-center justify-center">
                    <span class="text-xl">&times;</span>
                </button>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sport</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="award in modalAwards" :key="award.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900" x-text="award.user?.name || 'N/A'"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="award.sport?.name || 'N/A'"></td>
                                <td class="px-4 py-3 text-sm text-gray-600" x-text="award.event?.name || 'N/A'"></td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    <span x-text="award.medal" 
                                          :class="{
                                              'text-amber-600': award.medal === 'Gold',
                                              'text-gray-500': award.medal === 'Silver',
                                              'text-orange-700': award.medal === 'Bronze'
                                          }"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================= --}}
{{-- 📋 TOURNAMENT SECRETARIES TABLES --}}
{{-- ============================================================= --}}

@if(auth()->user()->role->name === 'Super Admin')
<div class="space-y-8 mb-10">
    
{{-- ================= PENDING AWARDS ================= --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-transparent px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Pending Award Submissions</h2>
            </div>

            {{-- EXPORT PDF --}}
            <a href="{{ route('dashboard.secretaries.export', 'pending') }}"
               class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
               Export PDF
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secretary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sport</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($secretariesPendingAwards as $secretary)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $secretary->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $secretary->email }}</td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ collect($secretary->sports_access ?? [])
                        ->map(fn($id)=>optional($sports->firstWhere('id',$id))->name)
                        ->filter()->implode(', ') ?: 'N/A' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ collect($secretary->sport_type_access ?? [])
                        ->map(fn($id)=>optional($sportTypes->firstWhere('id',$id))->type_name)
                        ->filter()->implode(', ') ?: 'N/A' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ implode(', ', $secretary->level_access ?? []) ?: 'N/A' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ implode(', ', $secretary->category_access ?? []) ?: 'N/A' }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-sm font-medium text-blue-700 bg-blue-50 rounded-full">
                            {{ $secretary->all_awards_pending_count + $secretary->individual_awards_pending_count }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- ================= NEEDS REVISION ================= --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="border-b border-gray-100 bg-gradient-to-r from-rose-50 to-transparent px-6 py-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Awards Needing Revision</h2>

            {{-- EXPORT --}}
            <a href="{{ route('dashboard.secretaries.export', 'revision') }}"
               class="text-sm bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg">
               Export PDF
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secretary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">All Awards</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Individual</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($secretaryRevisionList as $secretary)
                @php $total = $secretary->all_awards_revision_count + $secretary->individual_awards_revision_count; @endphp
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $secretary->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $secretary->email }}</td>
                    <td class="px-6 py-4 text-center text-rose-600 font-semibold">{{ $secretary->all_awards_revision_count }}</td>
                    <td class="px-6 py-4 text-center text-rose-600 font-semibold">{{ $secretary->individual_awards_revision_count }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 text-sm font-medium text-rose-700 bg-rose-50 rounded-full">{{ $total }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- ================= NO SUBMISSIONS ================= --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="border-b border-gray-100 bg-gradient-to-r from-amber-50 to-transparent px-6 py-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Secretaries Without Submissions</h2>

            {{-- EXPORT --}}
            <a href="{{ route('dashboard.secretaries.export', 'no_submissions') }}"
               class="text-sm bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg">
               Export PDF
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Secretary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sport</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($secretariesNoAwards as $secretary)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $secretary->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $secretary->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ collect($secretary->sports_access ?? [])
                        ->map(fn($id)=>optional($sports->firstWhere('id',$id))->name)
                        ->filter()->implode(', ') ?: 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ collect($secretary->sport_type_access ?? [])
                        ->map(fn($id)=>optional($sportTypes->firstWhere('id',$id))->type_name)
                        ->filter()->implode(', ') ?: 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ implode(', ', $secretary->level_access ?? []) ?: 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ implode(', ', $secretary->category_access ?? []) ?: 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>

</div>
@endif

{{-- ================= EVENT STATUS GRAPH ================= --}}
<div class="mb-10 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Event Status Overview</h2>
                <p class="text-sm text-gray-500">Distribution of events by current status</p>
            </div>
        </div>
        
        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-3 sm:mt-0">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                <span class="text-xs text-gray-600">Completed</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                <span class="text-xs text-gray-600">In Progress</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                <span class="text-xs text-gray-600">Pending</span>
            </div>
        </div>
    </div>

    <div class="relative w-full h-[320px]">
        <canvas id="eventStatusChart"></canvas>
    </div>
</div>

{{-- Newest Announcements --}}
<div class="mt-8">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900">Latest Announcements</h2>
        </div>
        
        @if(!$latestAnnouncements->isEmpty())
            <a href="{{ route('announcements.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-1">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @endif
    </div>

    @if($latestAnnouncements->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No announcements yet</h3>
            <p class="text-gray-500">Check back later for updates and news</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latestAnnouncements as $announcement)
                <a href="{{ route('announcements.index') }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                    {{-- Header with type indicator --}}
                    <div class="h-2 w-full bg-gradient-to-r from-amber-400 to-amber-500"></div>
                    
                    <div class="p-5">
                        {{-- Type and Event Badge --}}
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-medium rounded-full">
                                {{ $announcement->type }}
                            </span>
                            @if($announcement->event)
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                                    {{ $announcement->event->name }}
                                </span>
                            @endif
                        </div>

                        {{-- Title and Content --}}
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-amber-600 transition-colors">
                            {{ $announcement->title }}
                        </h3>
                        <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                            {{ Str::limit($announcement->content, 100) }}
                        </p>

                        {{-- Attachments Preview --}}
                        @if($announcement->files->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($announcement->files->take(3) as $file)
                                    @php
                                        $extension = strtolower($file->file_type);
                                        $url = asset('storage/' . $file->file_path);
                                    @endphp

                                    @if(in_array($extension, ['jpg','jpeg','png','gif']))
                                        <div class="relative group/attachment">
                                            <img src="{{ $url }}" alt="Attachment" 
                                                 class="w-10 h-10 object-cover rounded-lg border border-gray-200 group-hover/attachment:scale-110 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black/50 rounded-lg opacity-0 group-hover/attachment:opacity-100 transition-opacity flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 border border-gray-200 flex items-center justify-center group/attachment hover:scale-110 transition-transform duration-300">
                                            <span class="text-xs font-bold text-gray-600">{{ strtoupper($extension) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                                
                                @if($announcement->files->count() > 3)
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">+{{ $announcement->files->count() - 3 }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Footer with author and date --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-indigo-700">
                                        {{ substr($announcement->user->name ?? 'S', 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-600">{{ $announcement->user->name ?? 'System' }}</span>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ $announcement->published_at->setTimezone('Asia/Manila')->format('M d, Y · h:i A') }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Add this script for the chart if not already present --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('eventStatusChart').getContext('2d');
        
        // Sample data - replace with your actual data
        const eventStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending'],
                datasets: [{
                    data: [{{ $completedEvents ?? 0 }}, {{ $inProgressEvents ?? 0 }}, {{ $pendingEvents ?? 0 }}],
                    backgroundColor: [
                        '#10b981', // emerald-500
                        '#3b82f6', // blue-500
                        '#f59e0b'  // amber-500
                    ],
                    borderWidth: 0,
                    borderRadius: 8,
                    spacing: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#f9fafb',
                        bodyColor: '#e5e7eb',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush


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
