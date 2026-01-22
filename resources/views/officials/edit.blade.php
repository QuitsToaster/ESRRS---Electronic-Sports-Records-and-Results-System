@extends('layouts.app')

@section('page-title', 'Edit Official')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the details of the official below.
    </p>
</div>
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('officials.update', $official) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Official Name</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $official->name) }}"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sport --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Sport</label>
            <select name="sport"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                <option value="">-- Select Sport --</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->name }}"
                        {{ old('sport', $official->sport) === $sport->name ? 'selected' : '' }}>
                        {{ $sport->name }}
                    </option>
                @endforeach
            </select>
            @error('sport')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('officials.index') }}"
               class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
