@extends('layouts.app')

@section('page-title', 'Add Athlete')

@section('content')

<p class="text-sm text-gray-600 mb-4">
    Add a new athlete to the system. Profile photo is optional.
</p>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

<form action="{{ route('athletes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <hr class="my-6">

<h3 class="text-lg font-semibold mb-2">Bulk Upload Athletes</h3>
<p class="text-sm text-gray-600 mb-4">
    Upload an Excel (.xlsx) file with the following headings: 
    <code>first_name, middle_initial, last_name, sex, birthdate, school_id, sport_id, division, category, status</code>.
</p>

<form action="{{ route('athletes.bulk-upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <input type="file" name="file" accept=".xlsx,.csv" class="w-full border rounded px-3 py-2" required>
    </div>
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow">
        Upload Excel
    </button>
</form>


    {{-- Profile Photo --}}
    <div class="mb-4">
        <label class="block mb-1 font-medium">Profile Photo</label>
        <input type="file"
               name="profile_photo"
               accept="image/*"
               class="w-full border rounded px-3 py-2">
        <p class="text-xs text-gray-500 mt-1">
            JPG or PNG, max 2MB
        </p>
    </div>

    {{-- Name Fields --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block mb-1">First Name</label>
            <input type="text"
                   name="first_name"
                   value="{{ old('first_name') }}"
                   class="w-full border rounded px-3 py-2"
                   required>
        </div>

        <div>
            <label class="block mb-1">Middle Initial</label>
            <input type="text"
                   name="middle_initial"
                   value="{{ old('middle_initial') }}"
                   maxlength="1"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block mb-1">Last Name</label>
            <input type="text"
                   name="last_name"
                   value="{{ old('last_name') }}"
                   class="w-full border rounded px-3 py-2"
                   required>
        </div>
    </div>

    {{-- Sex & Birthdate --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block mb-1">Sex</label>
            <select name="sex" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Sex</option>
                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div>
            <label class="block mb-1">Birthdate</label>
            <input type="date"
                   name="birthdate"
                   value="{{ old('birthdate') }}"
                   class="w-full border rounded px-3 py-2">
        </div>
    </div>

    {{-- School --}}
    <div class="mb-3">
        <label class="block mb-1">School</label>
        <select name="school_id" class="w-full border rounded px-3 py-2" required>
            <option value="">Select School</option>
            @foreach($schools as $school)
                <option value="{{ $school->id }}"
                    {{ old('school_id') == $school->id ? 'selected' : '' }}>
                    {{ $school->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Sport --}}
    <div class="mb-3">
        <label class="block mb-1">Sport</label>
        <select name="sport_id" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Sport</option>
            @foreach($sports as $sport)
                <option value="{{ $sport->id }}"
                    {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                    {{ $sport->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Division --}}
    <div class="mb-3">
        <label class="block mb-1">Division</label>
        <select name="division" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Division</option>
            <option value="Elementary" {{ old('division') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
            <option value="Secondary" {{ old('division') == 'Secondary' ? 'selected' : '' }}>Secondary</option>
        </select>
    </div>

    {{-- Category --}}
    <div class="mb-3">
        <label class="block mb-1">Category</label>
        <select name="category" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Category</option>
            <option value="Boys" {{ old('category') == 'Boys' ? 'selected' : '' }}>Boys</option>
            <option value="Girls" {{ old('category') == 'Girls' ? 'selected' : '' }}>Girls</option>
        </select>
    </div>

    {{-- Status --}}
    <div class="mb-4">
        <label class="block mb-1">Status</label>
        <select name="status" class="w-full border rounded px-3 py-2" required>
            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Injured" {{ old('status') == 'Injured' ? 'selected' : '' }}>Injured</option>
            <option value="Disqualified" {{ old('status') == 'Disqualified' ? 'selected' : '' }}>Disqualified</option>
        </select>
    </div>

    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
        Add Athlete
    </button>
</form>

</div>
@endsection
