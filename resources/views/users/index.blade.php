@extends('layouts.app')

@section('page-title', 'Users Management')

@section('content')
{{-- ================= SUCCESS MODAL ================= --}}
@if(session('success'))
{{-- SUCCESS MODAL --}}
<div x-data="{ showSuccessModal: @js(session()->has('success')) }" x-show="showSuccessModal" x-cloak>
    <!-- Overlay -->
    <div x-show="showSuccessModal"
         x-transition.opacity
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>

    <!-- Modal Content -->
    <div x-show="showSuccessModal"
         x-transition
         @click.away="showSuccessModal = false"
         x-init="setTimeout(() => showSuccessModal = false, 5000)"
         class="fixed inset-0 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Success!</h2>
                <p class="text-gray-600">{{ session('success') }}</p>
                <button @click="showSuccessModal = false"
                        class="mt-6 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition duration-200 shadow-sm">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
@endif
<div class="space-y-6">
    {{-- ================= HEADER WITH GRADIENT ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Users Management</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Manage system users, roles, and permissions</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('users.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add User
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
                        View all registered users and their role permissions
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Use filters to find users by role, level, or division
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Edit user details or delete accounts as needed
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Reset passwords to default (ESRRS@2026)
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ================= ACCOUNT REQUESTS (SUPER ADMIN ONLY) ================= --}}
    @if(auth()->user()->role->name === 'Super Admin')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-transparent">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Account Requests</h2>
            </div>
        </div>

        @if($accountRequests->isEmpty())
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-gray-500">No account requests at this time.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($accountRequests as $accountRequest)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $accountRequest->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $accountRequest->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700 border border-purple-200">
                                        {{ str_replace('_', ' ', $accountRequest->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $accountRequest->message ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($accountRequest->status === 'done')
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">Processed</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $accountRequest->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($accountRequest->status !== 'done')
                                        <form action="{{ route('account-requests.done', $accountRequest->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition-all duration-200">
                                                Mark as Processed
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Processed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif

    {{-- ================= FILTERS & STATS ================= --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-3">
                {{-- Role Filter --}}
                <div class="relative">
                    <select name="role" class="w-full sm:w-40 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all appearance-none bg-white">
                        <option value="">All Roles</option>
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->id }}" {{ request('role') == $roleOption->id ? 'selected' : '' }}>
                                {{ $roleOption->name }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                {{-- Level Filter --}}
                <div class="relative">
                    <select name="level" class="w-full sm:w-40 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all appearance-none bg-white">
                        <option value="">All Levels</option>
                        @foreach(['Elementary','Secondary','Paragames'] as $levelOption)
                            <option value="{{ $levelOption }}" {{ request('level') == $levelOption ? 'selected' : '' }}>
                                {{ $levelOption }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                {{-- Division Filter --}}
                <div class="relative">
                    <select name="division" class="w-full sm:w-48 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all appearance-none bg-white">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $divOption)
                            <option value="{{ $divOption->name }}" {{ request('division') == $divOption->name ? 'selected' : '' }}>
                                {{ $divOption->name }}
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

                <a href="{{ route('users.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                    Clear
                </a>
            </form>

            {{-- Count Badge --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-sm text-gray-600">
                    <span class="font-semibold">Total Users:</span>
                    <span class="ml-1 font-bold text-gray-900">{{ $totalUsers }}</span>
                    @if(request()->hasAny(['role','level','division']))
                        <span class="ml-2 text-gray-400">| Filtered: <span class="font-bold text-gray-900">{{ $users->count() }}</span></span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    @if($users->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No users found</h3>
            <p class="text-gray-500 mb-4">Try adjusting your filters or add a new user</p>
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add User
            </a>
        </div>
    @else
        <div x-data="userActions()" class="relative bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Access</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sports</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sport Types</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->contact_number }}</td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700 border border-purple-200">
                                    {{ $user->role->name ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- MODULE ACCESS (SCROLLABLE) --}}
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="max-h-20 overflow-y-auto pr-1 custom-scrollbar">
                                    {{ $user->access ? implode(', ', $user->access) : '—' }}
                                </div>
                            </td>

                            {{-- LEVEL ACCESS (SCROLLABLE) --}}
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="max-h-20 overflow-y-auto pr-1 custom-scrollbar">
                                    {{ $user->level_access ? implode(', ', $user->level_access) : '—' }}
                                </div>
                            </td>

                            {{-- CATEGORY ACCESS (SCROLLABLE) --}}
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="max-h-20 overflow-y-auto pr-1 custom-scrollbar">
                                    {{ $user->category_access ? implode(', ', $user->category_access) : '—' }}
                                </div>
                            </td>

                            {{-- SPORT ACCESS (SCROLLABLE) --}}
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="max-h-20 overflow-y-auto pr-1 custom-scrollbar">
                                    @php
                                        $sportNames = collect($user->sports_access ?? [])
                                            ->map(fn($id) => optional($sports->firstWhere('id', $id))->name)
                                            ->filter()
                                            ->implode(', ');
                                    @endphp
                                    {{ $sportNames ?: '—' }}
                                </div>
                            </td>

                            {{-- SPORT TYPE ACCESS (SCROLLABLE) --}}
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="max-h-24 overflow-y-auto pr-1 border border-gray-200 rounded-lg p-2 bg-gray-50 custom-scrollbar">
                                    @php
                                        $typeNames = collect($user->sport_type_access ?? [])
                                            ->map(fn($id) => optional($sportTypes->firstWhere('id', $id))->type_name)
                                            ->filter()
                                            ->implode(', ');
                                    @endphp
                                    {{ $typeNames ?: '—' }}
                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-800 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>

                                    {{-- DELETE BUTTON WITH MODAL --}}
                                    <button type="button"
                                            @click="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>

                                    {{-- RESET PASSWORD BUTTON WITH MODAL --}}
                                    <button type="button"
                                            @click="openPasswordModal('{{ $user->email }}', {{ $user->id }})"
                                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        Reset Password
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($users, 'hasPages') && $users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->withQueryString()->links() }}
            </div>
            @endif


    {{-- DELETE CONFIRMATION MODAL --}}
    <div x-show="showDeleteModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        
        <div @click.away="showDeleteModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
            
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900 mb-2">Delete User</h2>
                <p class="text-gray-600 mb-2">
                    Are you sure you want to delete user <span class="font-semibold text-red-600" x-text="deleteUserName"></span>?
                </p>
                <p class="text-sm text-gray-500 mb-6">
                    This action cannot be undone.
                </p>
                
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                        Cancel
                    </button>
                    <form method="POST" :action="deleteFormAction" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- PASSWORD RESET CONFIRMATION MODAL --}}
    <div x-show="showPasswordModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        
        <div @click.away="showPasswordModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
            
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900 mb-2">Reset Password</h2>
                <p class="text-gray-600 mb-2">
                    Reset password for <span class="font-semibold text-blue-600" x-text="passwordEmail"></span>?
                </p>
                <p class="text-sm text-gray-500 mb-6">
                    Password will be reset to: <span class="font-mono font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">ESRRS@2026</span>
                </p>
                
                <div class="flex gap-3">
                    <button @click="showPasswordModal = false"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                        Cancel
                    </button>
                    <button @click="confirmPasswordReset()"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                        Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="showSuccessModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        
        <div @click.away="showSuccessModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900 mb-2">Success!</h2>
                <p class="text-gray-600 mb-6" x-text="successMessage"></p>
                
                <button @click="showSuccessModal = false"
                        class="w-full px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    OK
                </button>
            </div>
        </div>
    </div>

    {{-- ERROR MODAL --}}
    <div x-show="showErrorModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        
        <div @click.away="showErrorModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200">
            
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900 mb-2">Error</h2>
                <p class="text-gray-600 mb-6" x-text="errorMessage"></p>
                
                <button @click="showErrorModal = false"
                        class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
        </div>
    @endif

{{-- Custom scrollbar styles --}}
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

{{-- JS for user actions --}}
<script>
function userActions() {
    return {
        // Password reset modal
        showPasswordModal: false,
        passwordEmail: '',
        passwordUserId: null,
        
        // Delete modal
        showDeleteModal: false,
        deleteUserId: null,
        deleteUserName: '',
        deleteFormAction: '',
        
        // Success/Error modals
        showSuccessModal: false,
        successMessage: '',
        showErrorModal: false,
        errorMessage: '',

        openPasswordModal(email, id) {
            this.passwordEmail = email;
            this.passwordUserId = id;
            this.showPasswordModal = true;
        },

        openDeleteModal(id, name) {
            this.deleteUserId = id;
            this.deleteUserName = name;
            this.deleteFormAction = `{{ url('users') }}/${id}`;
            this.showDeleteModal = true;
        },

        confirmPasswordReset() {
            if (!this.passwordUserId) {
                this.showPasswordModal = false;
                this.errorMessage = "User ID missing";
                this.showErrorModal = true;
                return;
            }

            fetch(`{{ url('users') }}/${this.passwordUserId}/reset-password`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    password: "ESRRS@2026"
                })
            })
            .then(response => response.json())
            .then(data => {
                this.showPasswordModal = false;
                this.successMessage = data.message || "Password reset successfully";
                this.showSuccessModal = true;
            })
            .catch(error => {
                console.error(error);
                this.showPasswordModal = false;
                this.errorMessage = "Something went wrong resetting the password.";
                this.showErrorModal = true;
            });
        }
    }
}
</script>
@endsection