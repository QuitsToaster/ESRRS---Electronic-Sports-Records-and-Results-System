@extends('layouts.app')

@section('page-title', 'Layout Settings')

@section('content')
<div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 w-full">

    <h2 class="text-2xl font-semibold mb-4">Banner Images</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.layout.save') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <p class="text-gray-600">Upload at least 5 banner images. The order will be used in the slideshow.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @for($i = 0; $i < 5; $i++)
                <div class="flex flex-col items-center">
                    @php $banner = $banners[$i] ?? null; @endphp
                    <img src="{{ $banner ? asset('storage/' . $banner->image) : 'https://via.placeholder.com/300x150?text=Banner' }}" 
                         class="w-full h-40 object-cover rounded mb-2">
                    <input type="file" name="images[]" class="block w-full text-sm text-gray-500">
                </div>
            @endfor
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow font-semibold">
            Save Banners
        </button>
    </form>
</div>
@endsection
