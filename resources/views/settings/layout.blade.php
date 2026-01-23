@extends('layouts.app')

@section('page-title', 'Layout Settings')

@section('content')
<div class="space-y-8">

    {{-- Success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.layout.save') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- =======================
           ADMIN SIDEBAR LOGO
        ========================--}}
        <div class="bg-white border rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4">Admin Sidebar Logo</h2>

            <img
                src="{{ $adminLogo ? asset('storage/' . $adminLogo->value) : 'https://via.placeholder.com/200x200?text=Admin+Logo' }}"
                class="h-32 object-contain mb-3 border rounded p-2">

            <input type="file" name="admin_logo" class="block text-sm text-gray-600">
            <p class="text-sm text-gray-500 mt-1">Recommended: Square logo (PNG, transparent)</p>
        </div>

        {{-- =======================
           VIEWER TOP BAR LOGO
        ========================--}}
        <div class="bg-white border rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4">Viewer Top Bar Logo</h2>

            <img
                src="{{ $viewerLogo ? asset('storage/' . $viewerLogo->value) : 'https://via.placeholder.com/300x100?text=Viewer+Logo' }}"
                class="h-20 object-contain mb-3 border rounded p-2">

            <input type="file" name="viewer_logo" class="block text-sm text-gray-600">
            <p class="text-sm text-gray-500 mt-1">Recommended: Horizontal logo</p>
        </div>

       {{-- =======================
   SYSTEM DEVELOPERS
========================--}}
<div class="bg-white border rounded-lg p-6 shadow-sm">
    <h2 class="text-xl font-semibold mb-4">System Developers</h2>

    <p class="text-gray-600 mb-4">Add details of the system developers and system overview.</p>

    <div id="developers-container" class="space-y-4">
        @forelse($developers as $index => $dev)
            <div class="grid grid-cols-1 sm:grid-cols-7 gap-4 items-center border p-3 rounded relative">
                <input type="hidden" name="developer_id[]" value="{{ $dev->id }}">

                {{-- Photo --}}
                <div class="sm:col-span-1">
                    <img src="{{ $dev->photo ? asset('storage/' . $dev->photo) : 'https://via.placeholder.com/100?text=Photo' }}"
                         class="h-20 w-20 object-cover rounded mb-2">
                    <input type="file" name="developer_photo[]" class="block text-sm text-gray-600">
                </div>

                {{-- Name --}}
                <div class="sm:col-span-1">
                    <input type="text" name="developer_name[]" value="{{ $dev->name }}" placeholder="Name"
                           class="w-full border rounded px-2 py-1">
                </div>

                {{-- Email --}}
                <div class="sm:col-span-1">
                    <input type="email" name="developer_email[]" value="{{ $dev->email }}" placeholder="Email"
                           class="w-full border rounded px-2 py-1">
                </div>

                {{-- Contact --}}
                <div class="sm:col-span-1">
                    <input type="text" name="developer_contact[]" value="{{ $dev->contact }}" placeholder="Contact"
                           class="w-full border rounded px-2 py-1">
                </div>

                {{-- Role --}}
                <div class="sm:col-span-1">
                    <input type="text" name="developer_role[]" value="{{ $dev->role }}" placeholder="Role"
                           class="w-full border rounded px-2 py-1">
                </div>

                {{-- Description --}}
                <div class="sm:col-span-2">
                    <textarea name="developer_description[]" placeholder="System Overview"
                              class="w-full border rounded px-2 py-1">{{ $dev->description }}</textarea>
                </div>

                {{-- Remove Button --}}
                <button type="button" onclick="this.closest('div').remove()"
                        class="absolute top-2 right-2 text-red-600 font-bold">X</button>
            </div>
        @empty
            {{-- Empty row for new developer --}}
            <div class="grid grid-cols-1 sm:grid-cols-7 gap-4 items-center border p-3 rounded relative">
                <input type="hidden" name="developer_id[]" value="">
                <div class="sm:col-span-1">
                    <img src="https://via.placeholder.com/100?text=Photo"
                         class="h-20 w-20 object-cover rounded mb-2">
                    <input type="file" name="developer_photo[]" class="block text-sm text-gray-600">
                </div>
                <div class="sm:col-span-1">
                    <input type="text" name="developer_name[]" placeholder="Name" class="w-full border rounded px-2 py-1">
                </div>
                <div class="sm:col-span-1">
                    <input type="email" name="developer_email[]" placeholder="Email" class="w-full border rounded px-2 py-1">
                </div>
                <div class="sm:col-span-1">
                    <input type="text" name="developer_contact[]" placeholder="Contact" class="w-full border rounded px-2 py-1">
                </div>
                <div class="sm:col-span-1">
                    <input type="text" name="developer_role[]" placeholder="Role" class="w-full border rounded px-2 py-1">
                </div>
                <div class="sm:col-span-2">
                    <textarea name="developer_description[]" placeholder="System Overview" class="w-full border rounded px-2 py-1"></textarea>
                </div>
                <button type="button" onclick="this.closest('div').remove()" class="absolute top-2 right-2 text-red-600 font-bold">X</button>
            </div>
        @endforelse
    </div>

    {{-- Add Developer Button --}}
    <button type="button" onclick="addDeveloperRow()"
            class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow font-semibold">
        + Add Developer
    </button>
</div>

<script>
function addDeveloperRow() {
    const container = document.getElementById('developers-container');
    const row = document.createElement('div');
    row.classList = 'grid grid-cols-1 sm:grid-cols-7 gap-4 items-center border p-3 rounded relative';
    row.innerHTML = `
        <input type="hidden" name="developer_id[]" value="">
        <div class="sm:col-span-1">
            <img src="https://via.placeholder.com/100?text=Photo" class="h-20 w-20 object-cover rounded mb-2">
            <input type="file" name="developer_photo[]" class="block text-sm text-gray-600">
        </div>
        <div class="sm:col-span-1">
            <input type="text" name="developer_name[]" placeholder="Name" class="w-full border rounded px-2 py-1">
        </div>
        <div class="sm:col-span-1">
            <input type="email" name="developer_email[]" placeholder="Email" class="w-full border rounded px-2 py-1">
        </div>
        <div class="sm:col-span-1">
            <input type="text" name="developer_contact[]" placeholder="Contact" class="w-full border rounded px-2 py-1">
        </div>
        <div class="sm:col-span-1">
            <input type="text" name="developer_role[]" placeholder="Role" class="w-full border rounded px-2 py-1">
        </div>
        <div class="sm:col-span-2">
            <textarea name="developer_description[]" placeholder="System Overview" class="w-full border rounded px-2 py-1"></textarea>
        </div>
        <button type="button" onclick="this.closest('div').remove()" class="absolute top-2 right-2 text-red-600 font-bold">X</button>
    `;
    container.appendChild(row);
}
</script>


        {{-- =======================
           BANNER IMAGES
        ========================--}}
        <div class="bg-white border rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold mb-4">Banner Images</h2>
            <p class="text-gray-600 mb-4">Upload up to 5 banner images.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @for($i = 0; $i < 5; $i++)
                    @php $banner = $banners[$i] ?? null; @endphp
                    <div>
                        <img
                            src="{{ $banner ? asset('storage/' . $banner->image) : 'https://via.placeholder.com/300x150?text=Banner' }}"
                            class="w-full h-40 object-cover rounded mb-2 border">

                        <input type="file" name="images[]" class="block text-sm text-gray-600">
                    </div>
                @endfor
            </div>
        </div>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow font-semibold">
            Save Layout Settings
        </button>
    </form>
</div>
@endsection
