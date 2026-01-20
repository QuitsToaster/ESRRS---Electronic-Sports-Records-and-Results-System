@extends('layouts.app')

@section('page-title', 'Edit School')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit School</h1>
    <p class="text-sm text-gray-600 mb-6">
        Update the details of the school.
    </p>

    <form action="{{ route('schools.update', $school->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 w-full">
        @csrf
        @method('PUT')

        {{-- School Name --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Name</label>
            <input type="text" name="name" value="{{ old('name', $school->name) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Division --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Division</label>
            <select name="division" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Division</option>
                <option value="Elementary" {{ old('division', $school->division)=='Elementary' ? 'selected':'' }}>Elementary</option>
                <option value="Secondary" {{ old('division', $school->division)=='Secondary' ? 'selected':'' }}>Secondary</option>
            </select>
            @error('division') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Province --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Province</label>
            <input type="text" name="province" value="{{ old('province', $school->province) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- City --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">City</label>
            <input type="text" name="city" value="{{ old('city', $school->city) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Logo --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Logo</label>
            @if($school->logo)
                <img src="{{ asset('storage/'.$school->logo) }}" alt="{{ $school->name }}" class="w-20 h-20 object-cover rounded mb-2">
            @endif
            <input type="file" name="logo" accept="image/*"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('schools.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded shadow text-gray-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">Update</button>
        </div>
    </form>

</div>
@endsection
