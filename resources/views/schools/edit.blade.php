@extends('layouts.app')

@section('page-title', 'Edit School')

@section('content')

{{-- ================= ALERTS ================= --}}
@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
        <div class="font-semibold mb-2">Please fix the following errors:</div>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

{{-- ================= HEADER ================= --}}
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Edit the details of the school.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('schools.update', $school->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 w-full">
        @csrf
        @method('PUT')

        {{-- School Name --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Name</label>
            <input type="text" name="name" value="{{ old('name', $school->name) }}"
                   class="w-full border rounded px-3 py-2">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- School Type --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Type</label>
            <select name="school_type" class="w-full border rounded px-3 py-2">
                <option value="">Select Type</option>
                <option value="Public" {{ old('school_type', $school->school_type)=='Public' ? 'selected':'' }}>Public</option>
                <option value="Private" {{ old('school_type', $school->school_type)=='Private' ? 'selected':'' }}>Private</option>
            </select>
            @error('school_type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- School Address --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Address</label>
            <input type="text" name="school_address" value="{{ old('school_address', $school->school_address) }}"
                   class="w-full border rounded px-3 py-2">
            @error('school_address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Division --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Division</label>
            <select name="division" class="w-full border rounded px-3 py-2">
                <option value="">Select Division</option>
                <option value="Elementary" {{ old('division', $school->division)=='Elementary' ? 'selected':'' }}>Elementary</option>
                <option value="Secondary" {{ old('division', $school->division)=='Secondary' ? 'selected':'' }}>Secondary</option>
            </select>
            @error('division') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- School Division --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Division (City/Region)</label>
            <input type="text" name="school_division" value="{{ old('school_division', $school->school_division) }}"
                   class="w-full border rounded px-3 py-2">
            @error('school_division') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- School Code --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Code</label>
            <input type="text" name="school_code" value="{{ old('school_code', $school->school_code) }}"
                   class="w-full border rounded px-3 py-2">
            @error('school_code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Province --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">Province</label>
            <input type="text" name="province" value="{{ old('province', $school->province) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- City --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">City</label>
            <input type="text" name="city" value="{{ old('city', $school->city) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Logo --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Logo</label>
            @if($school->logo)
                <img src="{{ asset('storage/'.$school->logo) }}" alt="{{ $school->name }}" class="w-20 h-20 object-cover rounded mb-2">
            @endif
            <input type="file" name="logo" accept="image/*"
                   class="w-full border rounded px-3 py-2">
            @error('logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('schools.index') }}" class="px-4 py-2 bg-gray-200 rounded shadow text-gray-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow">Update</button>
        </div>
    </form>

</div>
@endsection