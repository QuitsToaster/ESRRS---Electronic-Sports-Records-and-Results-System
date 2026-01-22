@extends('layouts.viewer')

@section('page-title', 'Participants')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Participants</h1>
    <p class="text-gray-600 mb-6">Click on a participants to view coaches and athletes.</p>

    {{-- Search --}}
    <form method="GET" action="{{ route('viewer.participants') }}" class="mb-6 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search participants..."
               class="flex-1 border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold">
            Search
        </button>
        @if($search)
            <a href="{{ route('viewer.participants') }}" class="text-gray-500 hover:text-gray-700 px-2 py-2">
                Clear
            </a>
        @endif
    </form>

    {{-- Schools Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($schools as $school)
            <a href="{{ route('viewer.participants.show', $school->id) }}" 
               class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-lg transition flex flex-col items-center p-4">
                
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" 
                         alt="{{ $school->name }} Logo"
                         class="w-24 h-24 object-contain rounded-full mb-2 border border-gray-300">
                @endif

                <h2 class="text-lg font-bold text-gray-800 text-center">{{ $school->name }}</h2>
            </a>
        @empty
            <div class="col-span-full text-gray-500 text-center py-10">
                No participating schools found.
            </div>
        @endforelse
    </div>
</div>
@endsection
