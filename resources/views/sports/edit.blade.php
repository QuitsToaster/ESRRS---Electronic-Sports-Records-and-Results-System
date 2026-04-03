@extends('layouts.app')

@section('page-title', 'Edit Sport')

@section('content')
<div class="bg-white shadow rounded-lg p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Sport</h1>
    <p class="text-sm text-gray-600 mb-6">
        Update the sport information, its level, category, and types of game.
    </p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sports.update', $sport->id) }}" method="POST" class="space-y-6 w-full">
        @csrf
        @method('PUT')

        <!-- Sport Name -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Sport Name</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $sport->name) }}"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Level -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Level</label>
            <select name="level" required
                    class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- Select Level --</option>
                <option value="Elementary" {{ old('level', $sport->level) == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                <option value="Secondary" {{ old('level', $sport->level) == 'Secondary' ? 'selected' : '' }}>Secondary</option>
            </select>
        </div>

        <!-- Category -->
<div>
    <label class="block text-gray-700 font-semibold mb-2">Category</label>
    <select name="category" required
            class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">-- Select Category --</option>

        <option value="Boys" {{ old('category', $sport->category) == 'Boys' ? 'selected' : '' }}>
            Boys
        </option>

        <option value="Girls" {{ old('category', $sport->category) == 'Girls' ? 'selected' : '' }}>
            Girls
        </option>

        {{-- ✅ NEW --}}
        <option value="Mixed" {{ old('category', $sport->category) == 'Mixed' ? 'selected' : '' }}>
            Mixed
        </option>
    </select>
</div>

        <!-- Type of Game (Dynamic) -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Type of Game</label>
            <div id="typeFields" class="space-y-2">
                @forelse($sport->types as $type)
                    <div class="flex gap-2">
                        <input type="text"
                               name="type_of_game[]"
                               value="{{ $type->type_name }}"
                               class="flex-1 border border-gray-300 rounded px-3 py-2
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button"
                                onclick="removeField(this)"
                                class="px-3 bg-red-500 hover:bg-red-600 text-white rounded">
                            ×
                        </button>
                    </div>
                @empty
                    <div class="flex gap-2">
                        <input type="text"
                               name="type_of_game[]"
                               placeholder="e.g., Single Weapon"
                               class="flex-1 border border-gray-300 rounded px-3 py-2
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button"
                                onclick="removeField(this)"
                                class="px-3 bg-red-500 hover:bg-red-600 text-white rounded">
                            ×
                        </button>
                    </div>
                @endforelse
            </div>

            <button type="button"
                    onclick="addField()"
                    class="mt-3 inline-flex items-center px-4 py-2
                           bg-green-600 hover:bg-green-700
                           text-white text-sm font-medium rounded shadow">
                + Add Another Type
            </button>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('sports.index') }}"
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400
                      rounded shadow text-gray-700">
                Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700
                           text-white rounded shadow">
                Update
            </button>
        </div>
    </form>
</div>

<!-- Scripts -->
<script>
function addField() {
    const container = document.getElementById('typeFields');

    const div = document.createElement('div');
    div.classList.add('flex', 'gap-2');

    div.innerHTML = `
        <input type="text"
               name="type_of_game[]"
               placeholder="e.g., Dual Weapon"
               class="flex-1 border border-gray-300 rounded px-3 py-2
                      focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button"
                onclick="removeField(this)"
                class="px-3 bg-red-500 hover:bg-red-600 text-white rounded">
            ×
        </button>
    `;

    container.appendChild(div);
}

function removeField(button) {
    button.parentElement.remove();
}
</script>
@endsection
