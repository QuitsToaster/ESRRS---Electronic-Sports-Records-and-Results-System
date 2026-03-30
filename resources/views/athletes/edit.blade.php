@extends('layouts.app')

@section('page-title', 'Edit Athlete')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

<form action="{{ route('athletes.update',$athlete->id) }}" method="POST" enctype="multipart/form-data">

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
        <strong>Errors:</strong>
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@csrf
@method('PUT')

{{-- PHOTO --}}
<div class="mb-6">
    <label class="block font-semibold mb-1">Profile Photo</label>
    <div class="flex items-center gap-4">
        <img id="photoPreview"
             src="{{ $athlete->profile_photo ? asset('storage/'.$athlete->profile_photo) : asset('images/avatar-placeholder.png') }}"
             class="w-24 h-24 rounded-full border object-cover">
        <input type="file" name="profile_photo" accept="image/*"
               class="border rounded px-3 py-2"
               onchange="previewPhoto(event)">
    </div>
</div>

{{-- NAME --}}
<div class="grid grid-cols-3 gap-4 mb-4">
    <div>
        <label class="font-medium">First Name</label>
        <input name="first_name" value="{{ old('first_name',$athlete->first_name) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="font-medium">Middle Initial</label>
        <input name="middle_initial"
               value="{{ old('middle_initial',$athlete->middle_initial) }}"
               maxlength="1"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="font-medium">Last Name</label>
        <input name="last_name" value="{{ old('last_name',$athlete->last_name) }}"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

{{-- SEX & BIRTHDATE --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="font-medium">Sex</label>
        <select name="sex" class="w-full border rounded px-3 py-2">
            <option value="Male" {{ $athlete->sex=='Male'?'selected':'' }}>Male</option>
            <option value="Female" {{ $athlete->sex=='Female'?'selected':'' }}>Female</option>
        </select>
    </div>

    <div>
        <label class="font-medium">Birthdate</label>
        <input type="date" name="birthdate"
               value="{{ old('birthdate',$athlete->birthdate) }}"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

{{-- SCHOOL INFO --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="font-medium">Region</label>
        <input name="region"
               value="{{ old('region',$athlete->region) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="font-medium">School Type</label>
        <select name="school_type" class="w-full border rounded px-3 py-2">
            <option value="">-- Select --</option>
            <option value="Public" {{ $athlete->school_type=='Public'?'selected':'' }}>Public</option>
            <option value="Private" {{ $athlete->school_type=='Private'?'selected':'' }}>Private</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="font-medium">School Address</label>
        <input name="school_address"
               value="{{ old('school_address',$athlete->school_address) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="font-medium">Municipality / Town / City</label>
        <input name="municipality"
               value="{{ old('municipality',$athlete->municipality) }}"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

{{-- ✅ NEW: LEGISLATIVE DISTRICT --}}
<div class="mb-4">
    <label class="font-medium">Legislative District</label>
    <input name="legislative_district"
           value="{{ old('legislative_district',$athlete->legislative_district) }}"
           placeholder="e.g., 1st District"
           class="w-full border rounded px-3 py-2">
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="font-medium">School Division</label>
        <input name="school_division"
               value="{{ old('school_division',$athlete->school_division) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="font-medium">School Code</label>
        <input name="school_code"
               value="{{ old('school_code',$athlete->school_code) }}"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

<div class="mb-4">
    <label class="font-medium">LRN</label>
    <input name="lrn"
           value="{{ old('lrn',$athlete->lrn) }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- TEAM --}}
<div class="mb-4">
    <label class="font-medium">Team / School</label>
    <select name="school_id" class="w-full border rounded px-3 py-2">
        @foreach($schools as $school)
            <option value="{{ $school->id }}" {{ $athlete->school_id==$school->id?'selected':'' }}>
                {{ $school->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- SPORT --}}
<div class="mb-4">
    <label class="font-medium">Select Existing Sport</label>
    <select name="sport_id" class="w-full border rounded px-3 py-2">
        <option value="">-- Select Sport --</option>
        @foreach($sports as $sport)
            <option value="{{ $sport->id }}" {{ $athlete->sport_id==$sport->id?'selected':'' }}>
                {{ $sport->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="font-medium">Or Add New Sport</label>
    <input type="text"
           name="sport_name"
           placeholder="Type sport if not listed"
           class="w-full border rounded px-3 py-2">
</div>

{{-- DIVISION & CATEGORY --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="font-medium">Level</label>
        <select name="division" class="w-full border rounded px-3 py-2">
            <option value="Elementary" {{ $athlete->division=='Elementary'?'selected':'' }}>Elementary</option>
            <option value="Secondary" {{ $athlete->division=='Secondary'?'selected':'' }}>Secondary</option>
            <option value="Paragames" {{ $athlete->division=='Paragames'?'selected':'' }}>ParaGames</option>
        </select>
    </div>

    <div>
        <label class="font-medium">Category</label>
        <select name="category" class="w-full border rounded px-3 py-2">
            <option value="Boys" {{ $athlete->category=='Boys'?'selected':'' }}>Boys</option>
            <option value="Girls" {{ $athlete->category=='Girls'?'selected':'' }}>Girls</option>
        </select>
    </div>
</div>

{{-- STATUS --}}
<div class="mb-6">
    <label class="font-medium">Status</label>
    <select name="status" class="w-full border rounded px-3 py-2">
        <option value="Active" {{ $athlete->status=='Active'?'selected':'' }}>Active</option>
        <option value="Injured" {{ $athlete->status=='Injured'?'selected':'' }}>Injured</option>
        <option value="Disqualified" {{ $athlete->status=='Disqualified'?'selected':'' }}>Disqualified</option>
    </select>
</div>

{{-- ✅ QR CODE VALUE (MANUAL EDIT) --}}
<div class="mb-6">
    <label class="font-medium">QR Code Value</label>
    
    <input name="qr_code"
           value="{{ old('qr_code', $athlete->qr_code) }}"
           class="w-full border rounded px-3 py-2"
           placeholder="Paste original QR code value here">

    <p class="text-sm text-gray-500 mt-1">
        ⚠️ Important: This must match the QR used in attendance scanning.
    </p>

    {{-- OPTIONAL: SHOW CURRENT QR --}}
    @if($athlete->qr_code)
        <div class="mt-3">
            <label class="text-sm text-gray-600">Current QR Preview:</label>
            <img src="{{ route('athletes.qr', $athlete->id) }}"
                 class="w-32 h-32 border mt-1">
        </div>
    @endif
</div>

<button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
    Update Athlete
</button>

</form>
</div>

<script>
function previewPhoto(e){
    const reader = new FileReader();
    reader.onload = () => photoPreview.src = reader.result;
    reader.readAsDataURL(e.target.files[0]);
}
</script>
@endsection