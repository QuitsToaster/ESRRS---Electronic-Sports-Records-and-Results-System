@extends('layouts.app')

@section('page-title', 'Add Athlete')

@section('content')

<p class="text-sm text-gray-600 mb-4">
    Add a new athlete. You may type a new school or sport if not yet registered.
</p>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

<form action="{{ route('athletes.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- ================= ATHLETE INFO ================= --}}
<h3 class="text-lg font-semibold mb-3">Athlete Information</h3>

<div class="mb-4">
    <label class="block mb-1 font-medium">Profile Photo</label>
    <input type="file" name="profile_photo" accept="image/*"
           class="w-full border rounded px-3 py-2">
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div>
        <label>First Name</label>
        <input name="first_name" class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
        <label>Middle Initial (no dot)</label>
        <input name="middle_initial"
               pattern="[A-Za-z]"
               maxlength="1"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>Last Name</label>
        <input name="last_name" class="w-full border rounded px-3 py-2" required>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label>Sex</label>
        <select name="sex" class="w-full border rounded px-3 py-2" required>
            <option value="">Select</option>
            <option>Male</option>
            <option>Female</option>
        </select>
    </div>

    <div>
        <label>Birthdate</label>
        <input type="date" name="birthdate"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

<hr class="my-6">

{{-- ================= SCHOOL INFO ================= --}}
<h3 class="text-lg font-semibold mb-3">School Information</h3>

<div class="mb-4">
    <label>Select Existing School</label>
    <select name="school_id" class="w-full border rounded px-3 py-2">
        <option value="">-- Select School --</option>
        @foreach($schools as $school)
            <option value="{{ $school->id }}">{{ $school->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label>Or Add New School</label>
    <input name="school_name"
           placeholder="Type school name if not listed"
           class="w-full border rounded px-3 py-2">
</div>

{{-- ✅ NEW: SCHOOL LOGO --}}
<div class="mb-4">
    <label class="font-medium">
        School Logo <span class="text-sm text-gray-500">(for new schools only)</span>
    </label>
    <input type="file" name="school_logo"
           accept="image/png,image/jpeg"
           class="w-full border rounded px-3 py-2">
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label>Region</label>
        <input name="region" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>School Type</label>
        <select name="school_type" class="w-full border rounded px-3 py-2">
            <option value="">Select</option>
            <option value="Public">Public</option>
            <option value="Private">Private</option>
        </select>
    </div>
</div>

<div class="mb-4">
    <label>School Address</label>
    <input name="school_address" class="w-full border rounded px-3 py-2">
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div>
        <label>School Division</label>
        <input name="school_division" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>School Code</label>
        <input name="school_code" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>LRN</label>
        <input name="lrn" maxlength="20"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

<hr class="my-6">

{{-- ================= SPORT INFO ================= --}}
<h3 class="text-lg font-semibold mb-3">Sports Information</h3>

<div class="mb-4">
    <label>Select Existing Sport</label>
    <select name="sport_id" class="w-full border rounded px-3 py-2">
        <option value="">-- Select Sport --</option>
        @foreach($sports as $sport)
            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label>Or Add New Sport</label>
    <input name="sport_name"
           placeholder="Type sport if not listed"
           class="w-full border rounded px-3 py-2">
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div>
        <label>Division</label>
        <select name="division" class="w-full border rounded px-3 py-2">
            <option>Elementary</option>
            <option>Secondary</option>
        </select>
    </div>

    <div>
        <label>Category</label>
        <select name="category" class="w-full border rounded px-3 py-2">
            <option>Boys</option>
            <option>Girls</option>
        </select>
    </div>

    <div>
        <label>Status</label>
        <select name="status" class="w-full border rounded px-3 py-2">
            <option>Active</option>
            <option>Injured</option>
            <option>Disqualified</option>
        </select>
    </div>
</div>

<button class="bg-blue-600 text-white px-6 py-2 rounded shadow">
    Add Athlete
</button>

</form>
</div>
@endsection
