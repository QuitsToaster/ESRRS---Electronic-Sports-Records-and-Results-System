@extends('layouts.app')

@section('page-title', 'Add Photos')

@section('content')

<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Publish official photos by uploading them here. You can add titles and descriptions to each set of photos.
    </p>
</div>

@if($errors->any())
    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
        <ul>
            @foreach($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">
<form method="POST" action="{{ route('gallery.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" name="title" value="{{ old('title') }}" required
               class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500">
    </div>

    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3"
                  class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500">{{ old('description') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Upload Photos</label>
        <input type="file" name="photos[]" multiple required
               class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500">
        <p class="text-xs text-gray-500 mt-1">You can upload multiple images at once.</p>
    </div>

    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700">Folder</label>

        <select name="folder" id="folderSelect"
                class="mt-1 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500">
            <option value="">-- Select Existing Folder --</option>
            @foreach($existingFolders as $folder)
                <option value="{{ $folder }}" {{ old('folder') == $folder ? 'selected' : '' }}>{{ $folder }}</option>
            @endforeach
            <option value="new" {{ old('folder') == 'new' ? 'selected' : '' }}>+ Create New Folder</option>
        </select>

        <input type="text" name="new_folder" id="newFolderInput"
               placeholder="Enter new folder name"
               class="mt-2 block w-full border-gray-300 rounded shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-500 {{ old('folder') == 'new' ? '' : 'hidden' }}"
               value="{{ old('new_folder') }}">

        <p class="text-xs text-gray-500 mt-1">
            Choose an existing folder or create a new one.
        </p>
    </div>

    <script>
    document.getElementById('folderSelect').addEventListener('change', function() {
        const newFolderInput = document.getElementById('newFolderInput');
        if(this.value === 'new') {
            newFolderInput.style.display = 'block';
        } else {
            newFolderInput.style.display = 'none';
        }
    });
    </script>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Upload
    </button>
</form>
</div>
@endsection
