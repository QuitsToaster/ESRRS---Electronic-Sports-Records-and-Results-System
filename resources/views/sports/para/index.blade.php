@extends('layouts.app')

@section('page-title', 'Para Games')

@section('content')

{{-- ================= INSTRUCTIONS / NOTES ================= --}}
<div class="p-4 mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h3 class="font-semibold mb-1">How to Use This Page:</h3>
    <ul class="list-disc list-inside text-sm space-y-1">
        <li>View all registered sports and their corresponding game types.</li>
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
                  border-transparent text-gray-500 hover:text-gray-700">
            Regular Sports
        </a>

        <a href="{{ route('para-sports.index') }}"
           class="pb-3 border-b-2 font-semibold
                  border-blue-600 text-blue-600">
            Para Games
        </a>
    </nav>
</div>

<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-gray-600">
        List of para games available in the system.
    </p>

    <div class="flex gap-2">
        <button id="bulkDeleteBtn"
                type="button"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow hidden">
            Delete Selected
        </button>

        <a href="{{ route('para-sports.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            + Add Para Game
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- BULK DELETE FORM --}}
<form id="bulkDeleteForm" action="{{ route('para-sports.bulkDelete') }}" method="POST">
@csrf
@method('DELETE')

<div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
    <table class="min-w-full border-collapse">
        <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3">
                    <input type="checkbox" id="selectAll">
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">
                    Para Game Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">
                    Type of Game
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">
                    Category
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse($sports as $sport)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <input type="checkbox"
                               name="sports[]"
                               value="{{ $sport->id }}"
                               class="sport-checkbox">
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $sport->name }}
                    </td>

                    <td class="px-6 py-4">
                        @if($sport->types->count())
                            <div class="flex flex-wrap gap-2">
                                @foreach($sport->types as $type)
                                    <span class="px-3 py-1 text-xs font-medium
                                                 bg-purple-100 text-purple-700
                                                 rounded-full">
                                        {{ $type->type_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-gray-400 italic">
                                No type added
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $sport->category }}
                    </td>

                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('para-sports.edit', $sport->id) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium mr-3">
                            Edit
                        </a>

                        <form action="{{ route('para-sports.destroy', $sport->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Delete this para game?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-800 font-medium">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="px-6 py-6 text-center text-gray-500">
                        No para games found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</form>

{{-- JAVASCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.sport-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    function toggleBulkButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleBulkButton();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBulkButton);
    });

    bulkDeleteBtn.addEventListener('click', function () {
        if (confirm('Are you sure you want to delete selected para games?')) {
            bulkDeleteForm.submit();
        }
    });

});
</script>

@endsection
