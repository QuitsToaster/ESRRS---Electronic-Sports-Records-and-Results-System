@extends('layouts.app')

@section('page-title', 'Edit Gallery Photo')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">
    <h2 class="text-xl font-semibold mb-4">Edit Photo</h2>

    <form action="{{ route('gallery.update', $photo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ $photo->title }}" class="mt-1 block w-full border-gray-300 rounded p-2" required>
        </div>

        {{-- Folder --}}
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Folder</label>
            <select name="folder" id="folderSelect" class="mt-1 block w-full border-gray-300 rounded p-2">
                <option value="">-- Select Existing Folder --</option>
                @foreach($existingFolders as $f)
                    <option value="{{ $f }}" {{ $photo->folder == $f ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
                <option value="new">+ Create New Folder</option>
            </select>

            <input type="text" name="new_folder" id="newFolderInput" placeholder="Enter new folder name"
                   class="mt-2 block w-full border-gray-300 rounded p-2 {{ old('folder') === 'new' ? '' : 'hidden' }}">
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" class="mt-1 block w-full border-gray-300 rounded p-2" rows="4">{{ $photo->description }}</textarea>
        </div>

        {{-- Existing Photos --}}
        @if(!empty($photo->photos))
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">Existing Photos (select to delete)</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($photo->photos as $index => $p)
                    <div class="relative border rounded overflow-hidden">
                        <img src="{{ asset('storage/' . $p) }}" class="w-full h-32 object-cover">
                        <label class="absolute top-1 left-1 bg-red-600 text-white text-xs px-1 rounded cursor-pointer">
                            <input type="checkbox" name="delete_photos[]" value="{{ $index }}" class="mr-1">
                            Delete
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Add New Photos --}}
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Add New Photos</label>
            <input type="file" name="photos[]" multiple class="mt-1 block w-full">
            <p class="text-xs text-gray-500">Uploading new photos will append to existing ones.</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-2 mt-4">
            <a href="{{ route('gallery.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>

<script>
document.getElementById('folderSelect').addEventListener('change', function() {
    const newFolderInput = document.getElementById('newFolderInput');
    if(this.value === 'new') {
        newFolderInput.classList.remove('hidden');
    } else {
        newFolderInput.classList.add('hidden');
    }
});
</script>
@endsection
