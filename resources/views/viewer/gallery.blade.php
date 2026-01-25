@extends('layouts.viewer') 

@section('page-title', 'Gallery')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Official Gallery</h2>
            <p class="text-gray-600 mt-1 text-sm">Browse official photos uploaded by folder.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('viewer.gallery') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                   placeholder="Search photos..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('viewer.gallery') }}" class="text-gray-500 hover:text-gray-700 px-2 py-2 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if($photos->isEmpty())
        <p class="text-gray-500">No photos uploaded yet.</p>
    @else
        @php
            $folders = $photos->groupBy('folder');
        @endphp

        {{-- Folder View --}}
        <div id="folderView" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($folders as $folder => $folderPhotos)
                @php
                    $coverImage = $folderPhotos[0]->photos[0] ?? null;
                    $photoCount = count($folderPhotos);
                @endphp
                <div class="relative rounded-xl overflow-hidden shadow-lg cursor-pointer group hover:shadow-2xl transition"
                     onclick="openFolder('{{ $folder }}')">
                    @if($coverImage)
                        <img src="{{ asset('storage/' . $coverImage) }}" alt="{{ $folder }}" class="w-full h-56 object-cover transform group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-56 bg-gray-200 flex items-center justify-center text-gray-500">
                            No Image
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent opacity-60"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-semibold">{{ $folder }}</h3>
                        <p class="text-sm">{{ $photoCount }} photos</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Photo View --}}
        <div id="photoView" class="hidden mt-6">
            <button onclick="backToFolders()"
                    class="mb-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
                &larr; Back to Folders
            </button>
            <h2 class="text-2xl font-bold mb-4" id="currentFolderTitle"></h2>
            <div id="photosGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>
        </div>
    @endif
</div>

{{-- PHOTO MODAL --}}
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 overflow-auto p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full relative flex flex-col">
        <button onclick="closePhotoModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>

        <div class="p-6 flex flex-col overflow-y-auto max-h-[90vh]" id="modalContent">
            {{-- Content injected by JS --}}
        </div>
    </div>
</div>

<script>
const photosData = @json($photos);
const foldersData = @json($photos->groupBy('folder'));

let currentPhotoIndex = 0;
let currentPhotoSet = null;

function openFolder(folderName) {
    const folderPhotos = foldersData[folderName];
    if(!folderPhotos) return;

    const folderView = document.getElementById('folderView');
    const photoView = document.getElementById('photoView');
    const photosGrid = document.getElementById('photosGrid');
    const currentFolderTitle = document.getElementById('currentFolderTitle');

    folderView.classList.add('hidden');
    photoView.classList.remove('hidden');
    currentFolderTitle.textContent = folderName;

    photosGrid.innerHTML = '';
    folderPhotos.forEach(photo => {
        const firstImage = photo.photos[0] ?? null;
        const extraCount = (photo.photos.length || 0) - 1;

        const div = document.createElement('div');
        div.className = 'border rounded-lg overflow-hidden shadow-sm cursor-pointer relative hover:shadow-lg transition';
        div.onclick = () => openPhotoModal(photo.id);

        div.innerHTML = `
            <div class="p-2 font-semibold bg-gray-100">${photo.title}</div>
            ${firstImage ? `
                <div class="relative">
                    <img src="/storage/${firstImage}" alt="Photo" class="w-full h-48 object-cover rounded">
                    ${extraCount > 0 ? `<div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">+${extraCount}</div>` : ''}
                </div>
            ` : `
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
            `}
        `;
        photosGrid.appendChild(div);
    });
}

function backToFolders() {
    document.getElementById('photoView').classList.add('hidden');
    document.getElementById('folderView').classList.remove('hidden');
}

// Photo Modal functions
function openPhotoModal(id) {
    const photo = photosData.find(p => p.id === id);
    if(!photo) return;

    currentPhotoIndex = 0;
    currentPhotoSet = photo;
    const modal = document.getElementById('photoModal');
    const content = document.getElementById('modalContent');

    function renderImage() {
        let html = `<h2 class="text-xl font-semibold mb-2">${photo.title}</h2>`;
        html += `<p class="text-gray-600 mb-4">${photo.description}</p>`;
        html += `<div class="relative w-full h-96 flex items-center justify-center mb-4">`;
        html += `<img src="/storage/${photo.photos[currentPhotoIndex]}" class="w-full h-full object-cover rounded">`;

        if(photo.photos.length > 1) {
            html += `
                <button onclick="prevImage()" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-70">&larr;</button>
                <button onclick="nextImage()" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-70">&rarr;</button>
                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                    ${currentPhotoIndex + 1} / ${photo.photos.length}
                </div>
            `;
        }

        html += `</div>`;
        content.innerHTML = html;
    }

    window.prevImage = function() {
        if(currentPhotoIndex > 0) {
            currentPhotoIndex--;
            renderImage();
        }
    }

    window.nextImage = function() {
        if(currentPhotoIndex < photo.photos.length - 1) {
            currentPhotoIndex++;
            renderImage();
        }
    }

    renderImage();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentPhotoIndex = 0;
    currentPhotoSet = null;
}
</script>
@endsection
