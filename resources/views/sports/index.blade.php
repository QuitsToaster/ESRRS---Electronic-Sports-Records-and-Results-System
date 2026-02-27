@extends('layouts.app')

@section('page-title', 'Sports')

@section('content')

{{-- Instructions / Notes --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>View all registered sports, their levels, categories, and game types.</li>
        <li>Filter the sport using the dropdowns by level and category.</li>
        <li>Use the checkboxes to select multiple sports for bulk deletion.</li>
        <li>Click <strong>+ Add Sport</strong> to register a new sport.</li>
        <li>Edit or delete individual sports using the Actions column.</li>
        <li>Use the tabs above to switch between Regular Sports and Para Games.</li>
    </ul>
</div>

{{-- Tabs --}}
<div class="mb-6 border-b border-gray-200">
    <nav class="flex gap-6">
        <a href="{{ route('sports.index') }}"
           class="pb-3 border-b-2 font-semibold
                  {{ request()->routeIs('sports.index') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Regular Sports
        </a>
        <a href="{{ route('para-sports.index') }}"
           class="pb-3 border-b-2 font-semibold
                  border-transparent text-gray-500 hover:text-gray-700">
            Para Games
        </a>
    </nav>
</div>

{{-- Filters & Total Count --}}
<div class="flex justify-between items-center mb-4">
    <div class="flex gap-3 items-center">
        <form method="GET" action="{{ route('sports.index') }}" class="flex gap-2 items-center">
            <select name="level" class="border rounded px-2 py-1">
                <option value="">All Levels</option>
                <option value="Elementary" {{ ($level ?? '') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                <option value="Secondary" {{ ($level ?? '') == 'Secondary' ? 'selected' : '' }}>Secondary</option>
            </select>
            <select name="category" class="border rounded px-2 py-1">
                <option value="">All Categories</option>
                <option value="Boys" {{ ($category ?? '') == 'Boys' ? 'selected' : '' }}>Boys</option>
                <option value="Girls" {{ ($category ?? '') == 'Girls' ? 'selected' : '' }}>Girls</option>
            </select>
            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
            <a href="{{ route('sports.index') }}" 
               class="px-3 py-1 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 ml-1">
               Reset
            </a>
        </form>
        <span class="text-sm text-gray-600 ml-4">Total Sports: {{ $totalSports }}</span>
    </div>
    <div class="flex gap-2">
        <button id="bulkDeleteBtn"
                type="button"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow hidden">
            Delete Selected
        </button>
        <a href="{{ route('sports.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Add Sport
        </a>
    </div>
</div>

{{-- Bulk Upload (Super Admin Only) --}}
@if(auth()->check() && auth()->user()->role->name === 'Super Admin')
<div class="p-4 mb-6 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded shadow-sm">
    <h3 class="font-semibold mb-2">Bulk Upload Sports (CSV)</h3>
    <form action="{{ route('sports.bulkUpload') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center">
        @csrf
        <input type="file" name="file" accept=".csv" class="border rounded px-2 py-1" required>
        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded shadow">
            Upload CSV
        </button>
        <a href="{{ asset('templates/sports_template.csv') }}" class="text-sm text-blue-600 hover:underline ml-3">
            Download CSV Template
        </a>
    </form>
    <p class="text-xs text-gray-600 mt-1">CSV headers must be: <strong>Name, Type of Sport, Level, Category</strong></p>
</div>
@endif

@if(session('success'))
<div class="bg-green-100 text-green-800 p-4 rounded mb-4">
    {{ session('success') }}
</div>
@endif

{{-- Sports Table --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3"><input type="checkbox" id="selectAll"></th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Sport Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Level</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Type of Game</th>
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($sports as $sport)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <input type="checkbox" value="{{ $sport->id }}" class="sport-checkbox">
                </td>
                <td class="px-6 py-4 font-medium">{{ $sport->name }}</td>
                <td class="px-6 py-4">{{ $sport->division ?? '-' }}</td>
                <td class="px-6 py-4">{{ $sport->category ?? '-' }}</td>
                <td class="px-6 py-4">
                    @forelse($sport->types as $type)
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full mr-1">{{ $type->type_name }}</span>
                    @empty
                        <span class="text-gray-400 italic">No type</span>
                    @endforelse
                </td>
                <td class="px-6 py-4 text-right flex justify-end gap-3">
                    <a href="{{ route('sports.edit', $sport->id) }}" class="text-blue-600">Edit</a>

                    {{-- Individual Delete --}}
                    <form action="{{ route('sports.destroy', $sport->id) }}" method="POST" onsubmit="return confirm('Delete this sport?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-6 text-gray-500">No sports found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Hidden Bulk Delete Form --}}
<form id="bulkDeleteForm" action="{{ route('sports.bulkDelete') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
    <input type="hidden" name="sports[]" id="bulkDeleteInput">
</form>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.sport-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkDeleteInput = document.getElementById('bulkDeleteInput');

    function toggleBulkButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleBulkButton();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', toggleBulkButton));

    bulkDeleteBtn.addEventListener('click', function () {
        const selectedIds = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedIds.length === 0) return;

        if (confirm('Are you sure you want to delete selected sports?')) {
            bulkDeleteInput.value = selectedIds;
            bulkDeleteForm.submit();
        }
    });
});
</script>

@endsection
