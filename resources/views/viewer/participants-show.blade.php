@extends('layouts.viewer')

@section('page-title', $division->name)

@section('content')

{{-- Division Header Section --}}
<section class="relative w-full -mt-16">
    <div class="absolute inset-0 -z-20">
        <img src="{{ asset('storage/arena.jpg') }}" 
             alt="{{ $division->name }} Background"
             class="w-full h-full object-cover brightness-50">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 -z-10">
        <span class="text-6xl sm:text-8xl md:text-9xl font-bold text-white opacity-20 select-none whitespace-nowrap">
            {{ $division->name }}
        </span>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 pt-48 pb-20 flex flex-col sm:flex-row items-center sm:items-center justify-center sm:justify-start gap-8">
        {{-- Back Button --}}
        <a href="{{ route('viewer.participants') }}"
           class="absolute top-20 left-4 bg-white/30 backdrop-blur-md text-blue-100 hover:text-white px-3 py-1 rounded shadow transition z-10">
            &larr; Back
        </a>

        {{-- Division Logo --}}
        @if($division->logo)
        <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-full overflow-hidden border-2 border-white/30">
            <img src="{{ asset('storage/' . $division->logo) }}"
                 alt="{{ $division->name }} Logo"
                 class="w-full h-full object-cover">
        </div>
        @endif

        {{-- Division Name --}}
        <h1 class="text-7xl sm:text-7xl font-bold text-white text-center sm:text-left z-10">
            {{ $division->name }}
        </h1>
    </div>
</section>

{{-- Medals Section --}}
<section class="relative mx-auto px-4 py-10 bg-gray-300">
    {{-- Section Title --}}
    <div class="text-center mb-8">
        <h2 class="text-5xl md:text-8xl text-black italic font-bold" style="font-family: 'Poppins', sans-serif;">
            Medals
        </h2>
    </div>

    {{-- Medal Counts --}}
    @php
        $goldCount   = $division->results()->where('medal', 'Gold')->count();
        $silverCount = $division->results()->where('medal', 'Silver')->count();
        $bronzeCount = $division->results()->where('medal', 'Bronze')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        @foreach(['Gold' => $goldCount, 'Silver' => $silverCount, 'Bronze' => $bronzeCount] as $medal => $count)
            @php
                $bgGradient = $medal === 'Gold' ? 'bg-gradient-to-tr from-yellow-400 to-yellow-200' :
                              ($medal === 'Silver' ? 'bg-gradient-to-tr from-gray-300 to-gray-100' :
                              'bg-gradient-to-tr from-orange-400 to-yellow-300');
                $textColor = $medal === 'Gold' ? 'text-yellow-700' :
                             ($medal === 'Silver' ? 'text-gray-700' : 'text-orange-600');
            @endphp

            <div class="{{ $bgGradient }} rounded-lg p-6 flex flex-col items-center shadow-lg">
                <span class="text-lg font-semibold {{ $textColor }}">{{ $medal }}</span>
                <span class="text-3xl font-bold {{ $textColor }} mt-2 drop-shadow-lg">{{ $count }}</span>
            </div>
        @endforeach
    </div>

    {{-- Medal Table --}}
    <div class="overflow-x-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-black mb-4" style="font-family: 'Poppins', sans-serif;">
        Medal Summary
    </h2>
        <table class="min-w-full border border-gray-200 text-sm rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Medal</th>
                    <th class="px-3 py-2 text-left">Event</th>
                    <th class="px-3 py-2 text-left">Sport</th>
                    <th class="px-3 py-2 text-left">Division</th>
                    <th class="px-3 py-2 text-left">Category</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['Gold', 'Silver', 'Bronze'] as $medalType)
                    @foreach($division->results()->where('medal', $medalType)->get() as $result)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="px-3 py-2 font-semibold">{{ $medalType }}</td>
                            <td class="px-3 py-2">{{ $result->event->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $result->sport->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $result->division ?? 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $result->category ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endsection
