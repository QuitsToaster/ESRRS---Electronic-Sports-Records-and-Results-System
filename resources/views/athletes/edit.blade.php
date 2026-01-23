@extends('layouts.app')

@section('page-title', 'Edit Athlete')

@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the athlete's information by modifying the details below.
    </p>
</div>

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('athletes.update', $athlete->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- PROFILE PHOTO --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Profile Photo</label>

            <div class="flex items-center gap-4">
                <img
                    id="photoPreview"
                    src="{{ $athlete->profile_photo
                            ? asset('storage/'.$athlete->profile_photo)
                            : asset('images/avatar-placeholder.png') }}"
                    class="w-24 h-24 rounded-full object-cover border"
                    alt="Profile Photo"
                >

                <input type="file"
                       name="profile_photo"
                       accept="image/*"
                       class="border rounded px-3 py-2 w-full"
                       onchange="previewPhoto(event)">
            </div>

            <p class="text-xs text-gray-500 mt-1">
                Upload a new photo to replace the existing one (JPG, PNG).
            </p>
        </div>

        {{-- NAME --}}
        <div class="mb-3">
            <label class="block mb-1">Name</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $athlete->name) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- TEAM --}}
        <div class="mb-3">
            <label class="block mb-1">Team</label>
            <select name="school_id" class="w-full border rounded px-3 py-2">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}"
                        {{ $athlete->school_id == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SPORT --}}
        <div class="mb-3">
            <label class="block mb-1">Sport</label>
            <select name="sport_id" class="w-full border rounded px-3 py-2">
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}"
                        {{ $athlete->sport_id == $sport->id ? 'selected' : '' }}>
                        {{ $sport->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- DIVISION --}}
        <div class="mb-3">
            <label class="block mb-1">Division</label>
            <select name="division" class="w-full border rounded px-3 py-2">
                <option value="Elementary" {{ $athlete->division=='Elementary' ? 'selected' : '' }}>
                    Elementary
                </option>
                <option value="Secondary" {{ $athlete->division=='Secondary' ? 'selected' : '' }}>
                    Secondary
                </option>
            </select>
        </div>

        {{-- CATEGORY --}}
        <div class="mb-3">
            <label class="block mb-1">Category</label>
            <select name="category" class="w-full border rounded px-3 py-2">
                <option value="Boys" {{ $athlete->category=='Boys' ? 'selected' : '' }}>
                    Boys
                </option>
                <option value="Girls" {{ $athlete->category=='Girls' ? 'selected' : '' }}>
                    Girls
                </option>
            </select>
        </div>

        {{-- STATUS --}}
        <div class="mb-4">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Active" {{ $athlete->status=='Active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="Injured" {{ $athlete->status=='Injured' ? 'selected' : '' }}>
                    Injured
                </option>
                <option value="Disqualified" {{ $athlete->status=='Disqualified' ? 'selected' : '' }}>
                    Disqualified
                </option>
            </select>
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Update Athlete
        </button>
    </form>
</div>

{{-- IMAGE PREVIEW SCRIPT --}}
<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('photoPreview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
