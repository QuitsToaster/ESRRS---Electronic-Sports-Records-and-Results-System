@extends('layouts.app')

@section('page-title', 'Edit Announcement')

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Update the announcement details below.
    </p>
</div>

{{-- Form Card --}}
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

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

        {{-- Existing Attachments --}}
        @if($announcement->files->count())
            <div class="mb-6 border border-gray-200 rounded-lg p-4 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">
                    Existing Attachments
                </h3>

                <ul class="space-y-2 text-sm">
                    @foreach($announcement->files as $file)
                        <li class="flex items-center justify-between">
                            <a href="{{ asset('storage/'.$file->file_path) }}"
                               target="_blank"
                               class="text-blue-700 hover:underline">
                                {{ strtoupper($file->file_type) }} File
                            </a>

                            {{-- Individual delete checkbox --}}
                            <label class="flex items-center gap-2 text-red-600">
                                <input type="checkbox"
                                       name="delete_files[]"
                                       value="{{ $file->id }}"
                                       class="rounded border-gray-300">
                                Delete
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Attachments --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Add New Attachments
            </label>
            <input type="file"
                   name="files[]"
                   multiple
                   class="w-full border border-gray-300 rounded px-3 py-2">

            <p class="text-xs text-gray-500 mt-1">
                Uploading new files can automatically remove existing attachments.
            </p>

            {{-- Remove all existing attachments checkbox --}}
            @if($announcement->files->count())
                <div class="mt-3">
                    <label class="flex items-center gap-2 text-sm text-red-600">
                        <input type="checkbox"
                               name="remove_existing_files"
                               value="1"
                               class="rounded border-gray-300">
                        Remove all existing attachments when uploading new files
                    </label>
                </div>
            @endif
        </div>

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
