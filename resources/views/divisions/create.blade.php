@extends('layouts.app')

@section('page-title', 'Add Division')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Add Division</h2>

    <form action="{{ route('divisions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Division Name --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Division Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border p-2 rounded focus:outline-none focus:ring focus:border-blue-500" required>
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Logo --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Logo</label>
            <input type="file" name="logo" accept="image/*"
                   class="w-full border p-2 rounded focus:outline-none focus:ring focus:border-blue-500">
            @error('logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">
            Save Division
        </button>
    </form>
</div>
@endsection
