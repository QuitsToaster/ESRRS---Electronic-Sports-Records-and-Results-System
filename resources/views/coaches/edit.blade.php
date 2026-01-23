@extends('layouts.app')

@section('page-title', 'Edit Coach')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the details of the coach.
    </p>
</div>
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('coaches.update', $coach->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $coach->name) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-3">
            <label class="block mb-1">Team</label>
            <select name="school_id" class="w-full border rounded px-3 py-2">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $coach->school_id==$school->id?'selected':'' }}>{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">Sport</label>
            <select name="sport_id" class="w-full border rounded px-3 py-2">
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ $coach->sport_id==$sport->id?'selected':'' }}>{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Profile Photo --}}
        <div class="mb-3">
            <label class="block mb-1">Profile Photo</label>
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full overflow-hidden border border-gray-300">
                    <img id="photoPreview" src="{{ $coach->profile_photo ? asset('storage/' . $coach->profile_photo) : asset('images/avatar-placeholder.png') }}" alt="Profile Photo" class="w-full h-full object-cover">
                </div>
                <input type="file" name="profile_photo" accept="image/*" onchange="previewPhoto(event)">
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow">Update Coach</button>
    </form>
</div>

<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('photoPreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
