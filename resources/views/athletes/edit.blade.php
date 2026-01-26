@extends('layouts.app')

@section('page-title', 'Edit Athlete')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

<form action="{{ route('athletes.update',$athlete->id) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- PHOTO --}}
<div class="mb-4">
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
<div class="grid grid-cols-3 gap-3 mb-4">
    <div>
        <label>First Name</label>
        <input name="first_name" value="{{ old('first_name',$athlete->first_name) }}"
               class="w-full border rounded px-3 py-2">
    </div>
    <div>
    <label>Middle Initial (no dot ".")</label>
    <input name="middle_initial" 
           value="{{ old('middle_initial',$athlete->middle_initial) }}"
           class="w-full border rounded px-3 py-2"
           pattern="[A-Za-z]"
           title="Only letters are allowed"
           maxlength="1">
</div>


    <div>
        <label>Last Name</label>
        <input name="last_name" value="{{ old('last_name',$athlete->last_name) }}"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

{{-- SEX & BIRTHDATE --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label>Sex</label>
        <select name="sex" class="w-full border rounded px-3 py-2">
            <option value="Male" {{ $athlete->sex=='Male'?'selected':'' }}>Male</option>
            <option value="Female" {{ $athlete->sex=='Female'?'selected':'' }}>Female</option>
        </select>
    </div>
    <div>
        <label>Birthdate</label>
        <input type="date" name="birthdate"
               value="{{ old('birthdate',$athlete->birthdate) }}"
               class="w-full border rounded px-3 py-2">
    </div>
</div>

{{-- TEAM --}}
<div class="mb-3">
    <label>Team</label>
    <select name="school_id" class="w-full border rounded px-3 py-2">
        @foreach($schools as $school)
            <option value="{{ $school->id }}" {{ $athlete->school_id==$school->id?'selected':'' }}>
                {{ $school->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- SPORT --}}
<div class="mb-3">
    <label>Sport</label>
    <select name="sport_id" class="w-full border rounded px-3 py-2">
        @foreach($sports as $sport)
            <option value="{{ $sport->id }}" {{ $athlete->sport_id==$sport->id?'selected':'' }}>
                {{ $sport->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- DIVISION --}}
<div class="mb-3">
    <label>Division</label>
    <select name="division" class="w-full border rounded px-3 py-2">
        <option value="Elementary" {{ $athlete->division=='Elementary'?'selected':'' }}>Elementary</option>
        <option value="Secondary" {{ $athlete->division=='Secondary'?'selected':'' }}>Secondary</option>
    </select>
</div>

{{-- CATEGORY --}}
<div class="mb-3">
    <label>Category</label>
    <select name="category" class="w-full border rounded px-3 py-2">
        <option value="Boys" {{ $athlete->category=='Boys'?'selected':'' }}>Boys</option>
        <option value="Girls" {{ $athlete->category=='Girls'?'selected':'' }}>Girls</option>
    </select>
</div>

{{-- STATUS --}}
<div class="mb-4">
    <label>Status</label>
    <select name="status" class="w-full border rounded px-3 py-2">
        <option value="Active" {{ $athlete->status=='Active'?'selected':'' }}>Active</option>
        <option value="Injured" {{ $athlete->status=='Injured'?'selected':'' }}>Injured</option>
        <option value="Disqualified" {{ $athlete->status=='Disqualified'?'selected':'' }}>Disqualified</option>
    </select>
</div>

<button class="bg-blue-600 text-white px-6 py-2 rounded shadow">
    Update Athlete
</button>

</form>
</div>

<script>
function previewPhoto(e){
    const r = new FileReader();
    r.onload = () => photoPreview.src = r.result;
    r.readAsDataURL(e.target.files[0]);
}
</script>
@endsection
