@extends('layouts.app')

@section('page-title', 'Para Games Management')

@section('content')
<div class="space-y-6">
    {{-- ================= HEADER WITH GRADIENT ================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Para Games Management</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Manage all para games categories and game types</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('para-sports.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Para Game
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
                        View all registered para games and their game types
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Use checkboxes to select multiple para games for bulk deletion
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Click "Add Para Game" to register a new para sport
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span>
                        Switch between Regular Sports and Para Games using tabs
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-6">
            <a href="{{ route('sports.index') }}"
               class="pb-3 border-b-2 font-semibold text-sm transition-colors
                      border-transparent text-gray-500 hover:text-gray-700">
                Regular Sports
            </a>

            <a href="{{ route('para-sports.index') }}"
               class="pb-3 border-b-2 font-semibold text-sm transition-colors
                      border-blue-600 text-blue-600">
                Para Games
            </a>
        </nav>
    </div>

    {{-- ================= FILTERS & STATS ================= --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <form method="GET" action="{{ route('para-sports.index') }}">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

            {{-- LEFT SIDE --}}
            <div class="flex flex-wrap items-center gap-3">

                <span class="text-sm text-gray-600">
                    Manage para games and their categories
                </span>

                {{-- CATEGORY FILTER --}}
                <div class="relative">
                    <select name="category"
                            onchange="this.form.submit()"
                            class="w-full sm:w-40 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all appearance-none bg-white">

                        <option value="">All Categories</option>

                        <option value="Boys" {{ ($category ?? '') == 'Boys' ? 'selected' : '' }}>
                            Boys
                        </option>

                        <option value="Girls" {{ ($category ?? '') == 'Girls' ? 'selected' : '' }}>
                            Girls
                        </option>

                        <option value="Mixed" {{ ($category ?? '') == 'Mixed' ? 'selected' : '' }}>
                            Mixed
                        </option>
                    </select>

                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

            </div>

            {{-- RIGHT SIDE (COUNT) --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>

                <span class="text-sm text-gray-600">
                    <span class="font-semibold">Total Para Games:</span>
                    <span class="ml-1 font-bold text-gray-900">{{ $totalParaSports }}</span>
                </span>
            </div>

        </div>
    </form>
</div>

    {{-- ================= BULK UPLOAD (PARA GAMES) ================= --}}
@if(auth()->check() && auth()->user()->role->name === 'Super Admin')
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm">
    <div class="flex items-start gap-3">
        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
        </div>

        <div class="flex-1">
            <h3 class="font-semibold text-amber-900 mb-3">
                Bulk Upload Para Games (CSV)
            </h3>

            <form action="{{ route('sports.bulkUpload') }}" method="POST" enctype="multipart/form-data"
                  class="flex flex-wrap items-center gap-3">
                @csrf

                <input type="file"
                       name="file"
                       accept=".csv"
                       required
                       class="w-full sm:w-64 px-4 py-2.5 border border-amber-200 rounded-xl text-sm bg-white
                              focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition-all">

                <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600
                               hover:from-amber-600 hover:to-amber-700 text-white font-semibold rounded-xl shadow-md transition-all">
                    Upload CSV
                </button>
            </form>

            <p class="text-xs text-gray-600 mt-2">
                <span class="font-medium">CSV headers must be:</span>
                Name, Type of Sport, Level, Category
                <br>
                <span class="text-amber-700 font-medium">
                    (Level must be "Paragames")
                </span>
            </p>
        </div>
    </div>
</div>
@endif

    {{-- Success Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Para Games Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <form id="bulkDeleteForm" action="{{ route('para-sports.bulkDelete') }}" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 w-12">
                                <input type="checkbox" id="selectAll" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Para Game Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type of Game</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sports as $sport)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox"
                                           name="sports[]"
                                           value="{{ $sport->id }}"
                                           class="sport-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $sport->name }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($sport->types->count())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($sport->types as $type)
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full border border-blue-200">
                                                    {{ $type->type_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No type added</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $sport->category ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('para-sports.edit', $sport->id) }}"
                                           class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('para-sports.destroy', $sport->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this para game?')"
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
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No para games found</h3>
                                    <p class="text-gray-500 mb-4">Get started by adding your first para game</p>
                                    <a href="{{ route('para-sports.create') }}" 
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Para Game
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Bulk Delete Button (appears when items selected) --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <button id="bulkDeleteBtn"
                        type="button"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Selected
                </button>
                <span id="selectedCount" class="text-sm text-gray-500"></span>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript (unchanged functionality, only UI updated) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.sport-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const selectedCount = document.getElementById('selectedCount');

    function updateSelectedCount() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
        if (selectedCount) {
            selectedCount.textContent = checked > 0 ? `${checked} item(s) selected` : '';
        }
    }

    function toggleBulkButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (bulkDeleteBtn) {
            bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
        }
        updateSelectedCount();
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleBulkButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (selectAll) {
                selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            }
            toggleBulkButton();
        });
    });

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function () {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selectedIds.length === 0) return;

            if (confirm(`Are you sure you want to delete ${selectedIds.length} selected para game(s)?`)) {
                bulkDeleteForm.submit();
            }
        });
    }
});
</script>
@endsection