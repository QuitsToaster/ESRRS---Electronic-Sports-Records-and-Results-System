@extends('layouts.app')

@section('page-title', 'Add School')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Add a new school by filling out the form below.
    </p>
</div>
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('schools.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 w-full">
        @csrf

        {{-- School Name --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border rounded px-3 py-2">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- School Type --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Type</label>
            <select name="school_type" class="w-full border rounded px-3 py-2">
                <option value="">Select Type</option>
                <option value="Public" {{ old('school_type')=='Public' ? 'selected':'' }}>Public</option>
                <option value="Private" {{ old('school_type')=='Private' ? 'selected':'' }}>Private</option>
            </select>
        </div>

        {{-- School Address --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Address</label>
            <input type="text" name="school_address" value="{{ old('school_address') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Division (Dropdown) --}}
<div>
    <label class="block text-gray-700 font-semibold mb-1">Division</label>
    <select name="division" class="w-full border rounded px-3 py-2">
        <option value="">Select Division</option>
        <option value="Elementary" {{ old('division')=='Elementary' ? 'selected' : '' }}>Elementary</option>
        <option value="Secondary" {{ old('division')=='Secondary' ? 'selected' : '' }}>Secondary</option>
    </select>
</div>

{{-- School Division (Text) --}}
<div>
    <label class="block text-gray-700 font-semibold mb-1">School Division (City/Region)</label>
    <input type="text" name="school_division" value="{{ old('school_division') }}"
           class="w-full border rounded px-3 py-2">
</div>


        {{-- School Code --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Code</label>
            <input type="text" name="school_code" value="{{ old('school_code') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Logo --}}
        <div>
            <label class="block text-gray-700 font-semibold mb-1">School Logo</label>
            <input type="file" name="logo" accept="image/*"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('schools.index') }}" class="px-4 py-2 bg-gray-200 rounded shadow text-gray-700">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow">Save</button>
        </div>
    </form>

</div>
@endsection
