@extends('layouts.app')

@section('page-title', 'Edit Division')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">
    <h2 class="text-2xl font-bold mb-4">Edit Division</h2>

    <form action="{{ route('divisions.update', $division) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Division Name --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Division Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $division->name) }}"
                   class="w-full border p-2 rounded focus:outline-none focus:ring focus:border-blue-500" required>
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Current Logo & Upload --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Logo</label>
            @if($division->logo && Storage::disk('public')->exists($division->logo))
                <img src="{{ asset('storage/'.$division->logo) }}" class="h-16 w-16 object-cover mb-2 rounded border">
            @endif
            <input type="file" name="logo" accept="image/*"
                   class="w-full border p-2 rounded focus:outline-none focus:ring focus:border-blue-500">
            @error('logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">
            Update Division
        </button>
    </form>
</div>
@endsection
