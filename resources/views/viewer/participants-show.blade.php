@extends('layouts.viewer')

@section('page-title', $division->name)

@section('content')

{{-- ================= DIVISION HEADER ================= --}}
<section class="relative w-full -mt-16">
    <div class="absolute inset-0 -z-20">
        <img src="{{ asset('storage/arena.jpg') }}"
             class="w-full h-full object-cover brightness-50">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <div class="absolute right-4 top-1/2 -translate-y-1/2 -z-10">
        <span class="text-6xl sm:text-8xl md:text-9xl font-bold text-white opacity-20">
            {{ $division->name }}
        </span>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 pt-48 pb-20 flex items-center gap-8">
        <a href="{{ route('viewer.participants') }}"
           class="absolute top-20 left-4 bg-white/30 backdrop-blur-md px-3 py-1 rounded">
            ← Back
        </a>

        @if($division->logo)
            <div class="w-44 h-44 rounded-full overflow-hidden border-2 border-white/30">
                <img src="{{ asset('storage/'.$division->logo) }}" class="w-full h-full object-cover">
            </div>
        @endif

        <h1 class="text-7xl font-bold text-white">
            {{ $division->name }}
        </h1>
    </div>
</section>

{{-- ================= MEDALS SECTION ================= --}}
<section class="bg-gray-300 px-4 py-12">

    <div class="text-center mb-10">
        <h2 class="text-6xl md:text-8xl italic font-bold text-black">
            Medals
        </h2>
    </div>

    {{-- ================= MEDAL CARDS ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12 max-w-5xl mx-auto">
        @foreach(['Gold','Silver','Bronze'] as $medal)
            @php
                $count = $medalCounts[$medal] ?? 0;
                $style = $medal === 'Gold'
                    ? 'from-yellow-400 to-yellow-200 text-yellow-700'
                    : ($medal === 'Silver'
                        ? 'from-gray-300 to-gray-100 text-gray-700'
                        : 'from-orange-400 to-yellow-300 text-orange-700');
            @endphp

            <div class="bg-gradient-to-tr {{ $style }} rounded-xl p-8 shadow-lg text-center">
                <div class="text-xl font-semibold">{{ $medal }}</div>
                <div class="text-5xl font-bold mt-2">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    {{-- ================= MEDAL SUMMARY TABLE ================= --}}
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow overflow-x-auto">
        <h3 class="text-3xl font-bold px-6 py-4 bg-gray-100">
            Medal Summary
        </h3>

        <table class="min-w-full text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left">Medal</th>
                    <th class="px-4 py-3 text-left">Event</th>
                    <th class="px-4 py-3 text-left">Sport</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Date</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($awards as $award)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-bold
                            {{ $award->medal === 'Gold' ? 'text-yellow-600' :
                               ($award->medal === 'Silver' ? 'text-gray-600' : 'text-orange-600') }}">
                            {{ $award->medal }}
                        </td>
                        <td class="px-4 py-3">{{ $award->event?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $award->sport?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $award->category }}</td>
                        <td class="px-4 py-3">
                            {{ $award->date?->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No medals recorded for this division.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>

@endsection
