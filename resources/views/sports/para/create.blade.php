@extends('layouts.app')

@section('page-title', 'Add Para Game')

@section('content')
<div class="bg-white shadow rounded-lg p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Add New Para Game</h1>
    <p class="text-sm text-gray-600 mb-6">
        Fill in the para game details below.
    </p>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('para-sports.store') }}"
          method="POST"
          class="space-y-6 w-full">
        @csrf

        <!-- Para Game Name -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">
                Para Game Name
            </label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Type of Game (Dynamic) -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">
                Type of Game
            </label>

            <div id="typeFields" class="space-y-2">
                <div class="flex gap-2">
                    <input type="text"
                           name="type_of_game[]"
                           placeholder="e.g., Wheelchair Singles"
                           class="flex-1 border border-gray-300 rounded px-3 py-2
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button"
                            onclick="removeField(this)"
                            class="px-3 bg-red-500 hover:bg-red-600 text-white rounded">
                        ×
                    </button>
                </div>
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
            <a href="{{ route('para-sports.index') }}"
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400
                      rounded shadow text-gray-700">
                Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white rounded shadow">
                Save
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
               placeholder="e.g., Wheelchair Doubles"
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
