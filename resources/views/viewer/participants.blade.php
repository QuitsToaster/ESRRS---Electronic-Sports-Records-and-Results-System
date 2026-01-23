@extends('layouts.viewer')

@section('page-title', 'Participants')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Teams</h1>
            <p class="text-gray-600 mt-1">Click on a team to view coaches and athletes.</p>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('viewer.participants') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search teams..."
                   class="w-full sm:w-64 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow font-semibold transition">
                Search
            </button>
            @if($search)
                <a href="{{ route('viewer.participants') }}" class="text-gray-500 hover:text-gray-700 px-2 py-2 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Schools Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($schools as $school)
            <a href="{{ route('viewer.participants.show', $school->id) }}"
               class="group relative bg-white border border-gray-200 rounded-lg shadow hover:shadow-xl transition transform hover:-translate-y-1 p-5 flex flex-col items-center text-center">
                
                {{-- School Logo --}}
                @if($school->logo)
                    <div class="w-28 h-28 mb-4 rounded-full overflow-hidden border-2 border-gray-200">
                        <img src="{{ asset('storage/' . $school->logo) }}" 
                             alt="{{ $school->name }} Logo"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @else
                    <div class="w-28 h-28 mb-4 rounded-full flex items-center justify-center bg-gray-100 text-gray-400 border-2 border-gray-200">
                        <span class="text-xl font-semibold">N/A</span>
                    </div>
                @endif

                {{-- School Name --}}
                <h2 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $school->name }}</h2>

                {{-- Optional: Number of coaches or athletes --}}
                @if($school->coaches->count() || $school->athletes->count())
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $school->coaches->count() }} Coaches &middot; {{ $school->athletes->count() }} Athletes
                    </p>
                @endif
            </a>
        @empty
            <div class="col-span-full text-gray-500 text-center py-10">
                No participating schools found.
            </div>
        @endforelse
    </div>
</div>
@endsection
