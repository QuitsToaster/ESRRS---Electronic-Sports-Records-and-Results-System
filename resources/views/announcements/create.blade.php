@extends('layouts.app')

@section('page-title', 'Create Announcement')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Create Announcement</h1>
    <p class="text-sm text-gray-600 mt-1">
        Publish official notices, schedule changes, venue updates, or result documents
    </p>
</div>

@if ($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 p-4 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- Form Card --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <form action="{{ route('announcements.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Announcement Title <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="title"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Announcement Type <span class="text-red-500">*</span>
            </label>
            <select name="type"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                <option value="">-- Select Type --</option>
                <option value="General">General Announcement</option>
                <option value="Schedule Update">Schedule Update</option>
                <option value="Venue Update">Venue Update</option>
                <option value="Results">Results / Official Notice</option>
            </select>
        </div>

        {{-- Event --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Related Event (Optional)
            </label>
            <select name="event_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                <option value="">-- None --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Content --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Announcement Content <span class="text-red-500">*</span>
            </label>
            <textarea name="content"
                      rows="5"
                      required
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500"></textarea>
        </div>

        {{-- Attachments --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Attachments (Optional)
            </label>
            <input type="file"
                   name="files[]"
                   multiple
                   class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50">

            <p class="text-xs text-gray-500 mt-1">
                Upload photos, signed result sheets, or official documents (PDF, DOC, JPG, PNG).
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('announcements.index') }}"
               class="px-4 py-2 rounded border border-gray-400 text-gray-700 hover:bg-gray-100">
                Cancel
            </a>

            <button type="submit"
                    class="px-5 py-2 rounded bg-green-600 text-white font-semibold hover:bg-green-700 shadow">
                Post Announcement
            </button>
        </div>

    </form>
</div>

@endsection
