@extends('layouts.app')

@section('page-title', 'Edit Announcement')

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">✏️ Edit Announcement</h1>
    <p class="text-sm text-gray-600 mt-1">
        Update announcement details, attachments, or related event information.
    </p>
</div>

{{-- Form Card --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 max-w-4xl">

    <form action="{{ route('announcements.update', $announcement) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Announcement Title <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   name="title"
                   value="{{ old('title', $announcement->title) }}"
                   required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>

        {{-- Type --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Announcement Type <span class="text-red-500">*</span>
            </label>
            <select name="type"
                    required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                @foreach(['General','Schedule Update','Venue Update','Results'] as $type)
                    <option value="{{ $type }}"
                        {{ $announcement->type === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Event --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Related Event <span class="text-gray-400">(Optional)</span>
            </label>
            <select name="event_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
                <option value="">-- None --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}"
                        {{ $announcement->event_id === $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Content --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Announcement Content <span class="text-red-500">*</span>
            </label>
            <textarea name="content"
                      rows="5"
                      required
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">{{ old('content', $announcement->content) }}</textarea>
        </div>

        {{-- Add Attachments --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Add More Attachments
            </label>
            <input type="file"
                   name="files[]"
                   multiple
                   class="w-full border border-gray-300 rounded px-3 py-2">

            <p class="text-xs text-gray-500 mt-1">
                Upload additional photos, PDFs, or official documents.
            </p>
        </div>

        {{-- Existing Attachments --}}
        @if($announcement->files->count())
            <div class="mb-6 border border-gray-200 rounded-lg p-4 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">
                    Existing Attachments
                </h3>

                <ul class="list-disc list-inside text-sm text-blue-700">
                    @foreach($announcement->files as $file)
                        <li>
                            <a href="{{ asset('storage/'.$file->file_path) }}"
                               target="_blank"
                               class="hover:underline">
                                {{ strtoupper($file->file_type) }} File
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('announcements.index') }}"
               class="px-4 py-2 rounded border border-gray-400 text-gray-700 hover:bg-gray-100">
                Cancel
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-semibold shadow">
                Update Announcement
            </button>
        </div>

    </form>
</div>

@endsection
