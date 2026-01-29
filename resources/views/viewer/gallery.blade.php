@extends('layouts.viewer') 

@section('page-title', 'Gallery')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4" style="font-family: 'Poppins', sans-serif;">
        <div>
            <h2 class="text-6xl font-bold text-gray-800">This is the show.</h2>
            <p class="text-gray-800 mt-1 text-lg">Browse official photos.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('viewer.gallery') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                   placeholder="Search photos..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition">
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
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
                    <div class="absolute bottom-4 left-4 text-white" style="font-family: 'Poppins', sans-serif;">
                        <h3 class="text-xl font-semibold">{{ $folder }}</h3>
                        <p class="text-sm">{{ $photoCount }} photos</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Photo View --}}
        <div id="photoView" class="hidden mt-6">
            <button onclick="backToFolders()"
                    class="mb-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition" style="font-family: 'Poppins', sans-serif;">
                &larr; Back to Folders
            </button>
            <h2 class="text-3xl md:text-4xl font-bold mb-4" style="font-family: 'Poppins', sans-serif;" id="currentFolderTitle"></h2>
            <div id="photosGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>
        </div>
    @endif
</div>

{{-- PHOTO MODAL --}}
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50 flex flex-col items-center justify-center overflow-auto p-4">
    <button onclick="closePhotoModal()"
            class="absolute top-6 right-6 text-white text-4xl font-bold z-50 hover:text-gray-300 transition">&times;</button>

    <div id="modalMain" class="w-full max-w-6xl flex flex-col items-center gap-4 relative">
        <button id="prevBtn" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white text-3xl px-4 py-2 rounded hover:bg-opacity-70 z-50">&larr;</button>
        <button id="nextBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white text-3xl px-4 py-2 rounded hover:bg-opacity-70 z-50">&rarr;</button>

        <div class="w-full flex justify-center">
            <img id="modalImage" src="" class="w-full max-h-[80vh] object-contain transition-all duration-300 rounded">
        </div>

        <button id="showThumbnailsBtn" class="px-6 py-2 bg-gray-700 text-white font-semibold hover:bg-gray-600 rounded transition">
            Show Thumbnails
        </button>
        <div id="thumbnailsContainer" class="hidden w-full overflow-x-auto flex gap-2 mt-2 p-2 bg-black/50 rounded"></div>
    </div>
</div>

<script>
const photosData = @json($photos);
const foldersData = @json($photos->groupBy('folder'));

let currentPhotoIndex = 0;
let currentPhotoSet = null;

// Open folder
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
        div.className = 'relative rounded-lg overflow-hidden shadow-sm cursor-pointer hover:shadow-lg transition group';
        div.onclick = () => openPhotoModal(photo.id);

        if(firstImage) {
            div.innerHTML = `
                <img src="/storage/${firstImage}" alt="Photo" class="w-full h-48 object-cover rounded transform transition duration-300 group-hover:scale-105">
                ${extraCount > 0 ? `<div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">+${extraCount}</div>` : ''}
                <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black via-black/40 to-transparent p-3 opacity-0 group-hover:opacity-100 transition-all duration-500 text-white" style="font-family: 'Poppins', sans-serif;">
                    <p class="truncate font-semibold text-lg md:text-lg">${photo.title}</p>
                </div>
            `;
        } else {
            div.innerHTML = `<div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>`;
        }

        photosGrid.appendChild(div);
    });
}

function backToFolders() {
    document.getElementById('photoView').classList.add('hidden');
    document.getElementById('folderView').classList.remove('hidden');
}

// Open folder photo modal
function openPhotoModal(id) {
    // Find the folder that contains the clicked photo
    let folderPhotos = [];
    let clickedPhoto = null;

    for (const folderName in foldersData) {
        foldersData[folderName].forEach(photo => {
            if(photo.id === id){
                clickedPhoto = photo;
                // Collect all images from all photos in this folder
                photoFolder = foldersData[folderName];
                folderPhotos = [];
                photoFolder.forEach(p => {
                    p.photos.forEach(img => folderPhotos.push({photoId: p.id, src: img}));
                });
            }
        });
    }

    if(!clickedPhoto || folderPhotos.length === 0) return;

    currentPhotoSet = folderPhotos;
    // Set currentPhotoIndex to the clicked image
    currentPhotoIndex = folderPhotos.findIndex(p => p.photoId === clickedPhoto.id);

    const modal = document.getElementById('photoModal');
    const modalImage = document.getElementById('modalImage');
    const thumbnailsContainer = document.getElementById('thumbnailsContainer');
    const showThumbnailsBtn = document.getElementById('showThumbnailsBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    function renderMainImage() {
        modalImage.src = `/storage/${currentPhotoSet[currentPhotoIndex].src}`;
        updateNavigationButtons();
        if(!thumbnailsContainer.classList.contains('hidden')) renderThumbnails();
    }

    function updateNavigationButtons() {
        prevBtn.style.display = currentPhotoIndex > 0 ? 'block' : 'none';
        nextBtn.style.display = currentPhotoIndex < currentPhotoSet.length - 1 ? 'block' : 'none';
    }

    function renderThumbnails() {
        thumbnailsContainer.innerHTML = '';
        currentPhotoSet.forEach((p, index) => {
            const thumb = document.createElement('img');
            thumb.src = `/storage/${p.src}`;
            thumb.className = `h-20 object-cover cursor-pointer border-2 ${index === currentPhotoIndex ? 'border-blue-500' : 'border-transparent'} transition rounded`;
            thumb.onclick = () => { currentPhotoIndex = index; renderMainImage(); };
            thumbnailsContainer.appendChild(thumb);
        });
    }

    showThumbnailsBtn.onclick = () => thumbnailsContainer.classList.toggle('hidden');

    prevBtn.onclick = () => { if(currentPhotoIndex > 0){ currentPhotoIndex--; renderMainImage(); } };
    nextBtn.onclick = () => { if(currentPhotoIndex < currentPhotoSet.length - 1){ currentPhotoIndex++; renderMainImage(); } };

    renderMainImage();
    thumbnailsContainer.classList.add('hidden');
    modal.classList.remove('hidden');
}


function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
    currentPhotoIndex = 0;
    currentPhotoSet = null;
}


</script>
@endsection
