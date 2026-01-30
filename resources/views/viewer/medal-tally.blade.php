@extends('layouts.viewer')

@section('page-title', 'Medal Tally')

@section('content')
<section class="relative py-10 min-h-screen -mt-16">
    {{-- Background --}}
    <div class="absolute inset-0">
        <img src="{{ asset('storage/medalbg.jpg') }}"
             alt="Medal Background"
             class="w-full h-full object-cover brightness-75 blur-sm">
        <div class="absolute inset-0 bg-black/30"></div>
    </div>

    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto pt-20 px-6">

        {{-- ================== MEDAL TALLY BY DIVISION ================== --}}
        {{-- Header --}}
        <div class="hidden md:grid grid-cols-[40px_1fr_420px_96px] items-center mb-6 text-white">
            <div></div>
            <h2 class="text-3xl font-bold">Medal Tally by Division</h2>

            <div class="grid grid-cols-3 text-center font-semibold text-lg">
                <span class="text-yellow-400">Gold</span>
                <span class="text-gray-300">Silver</span>
                <span class="text-orange-400">Bronze</span>
            </div>

            <div class="text-center font-semibold text-4xl text-red-600">Total</div>
        </div>

        {{-- Mobile Header --}}
        <h2 class="md:hidden text-center text-3xl font-bold text-white mb-6">
            Medal Tally by Division
        </h2>

        @if($medalTally->isEmpty())
            <p class="text-gray-200 text-sm text-center">No divisions found.</p>
        @else
            <div class="grid gap-4 md:gap-6">
                @foreach($medalTally as $index => $division)
                    {{-- Desktop Row --}}
                    <div class="hidden md:grid grid-cols-[40px_1fr_420px_70px]
                                items-center p-4 md:p-6
                                bg-white/10 backdrop-blur-md
                                rounded-xl shadow-lg hover:shadow-2xl transition">
                        <div class="text-white font-bold text-lg text-center">{{ $index + 1 }}</div>

                        <div class="flex items-center gap-4">
                            @if($division->logo)
                                <img src="{{ asset('storage/' . $division->logo) }}"
                                     class="w-12 h-12 object-cover rounded-full border border-white/30">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                    {{ strtoupper(substr($division->name ?? 'NA',0,2)) }}
                                </div>
                            @endif
                            <span class="text-white font-semibold text-lg">{{ $division->name ?? 'Unknown' }}</span>
                        </div>

                        <div class="grid grid-cols-3 text-center font-extrabold text-4xl">
                            <span class="text-yellow-400">{{ $division->gold }}</span>
                            <span class="text-gray-300">{{ $division->silver }}</span>
                            <span class="text-orange-400">{{ $division->bronze }}</span>
                        </div>

                        <div class="relative h-full">
                            <div class="absolute top-0 bottom-0 left-0 w-[500px] -z-10
                                        bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>
                            <div class="relative text-center text-red-600 font-extrabold text-6xl py-4">
                                {{ $division->total }}
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Card --}}
                    <div class="md:hidden bg-white/10 backdrop-blur-md rounded-xl shadow-lg p-4 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="text-white font-bold text-lg">#{{ $index + 1 }}</span>
                            @if($division->logo)
                                <img src="{{ asset('storage/' . $division->logo) }}"
                                     class="w-10 h-10 object-cover rounded-full border border-white/30">
                            @endif
                            <span class="text-white font-semibold text-base truncate">
                                {{ $division->name ?? 'Unknown' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 text-center">
                            <div>
                                <p class="text-xs text-yellow-300">Gold</p>
                                <p class="text-3xl font-extrabold text-yellow-400">{{ $division->gold }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-300">Silver</p>
                                <p class="text-3xl font-extrabold text-gray-300">{{ $division->silver }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-orange-300">Bronze</p>
                                <p class="text-3xl font-extrabold text-orange-400">{{ $division->bronze }}</p>
                            </div>
                        </div>

                        <div class="relative rounded-lg overflow-hidden">
                            <div class="absolute inset-0
                                        bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>
                            <div class="relative text-center text-red-700 font-extrabold text-4xl py-2">
                                Total: {{ $division->total }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ================== ALL MEDALS AWARDED ================== --}}
        @php
            use App\Models\Result;
            $results = Result::with(['event', 'sport', 'division'])->latest()->get();
        @endphp

        <div class="mt-16">
            <h2 class="text-center text-6xl md:text-8xl font-extrabold mb-6"
                style="font-family: 'Kristi', cursive;
                       background: linear-gradient(90deg, #FFD700, #FFEA00, #FFC700);
                       -webkit-background-clip: text;
                       -webkit-text-fill-color: transparent;">
                All Medals Awarded
            </h2>

            @foreach(['Elementary','Secondary'] as $divisionName)
                <h3 class="text-3xl font-semibold text-white mb-4">{{ $divisionName }} Division</h3>

                @php
                    $divisionResults = $results->where('division', $divisionName);
                @endphp

                @if($divisionResults->isEmpty())
                    <p class="text-gray-200 mb-6">No medals awarded yet for {{ $divisionName }} division.</p>
                @else
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full bg-white/10 backdrop-blur-md rounded-xl text-white shadow-lg">
                            <thead class="bg-white/20">
                                <tr>
                                    <th class="px-4 py-2 text-left">Event</th>
                                    <th class="px-4 py-2 text-left">Sport</th>
                                    <th class="px-4 py-2 text-left">Division</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-left">Medal</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($divisionResults as $result)
                                    <tr class="border-b border-white/20 hover:bg-white/10 transition">
                                        <td class="px-4 py-2">{{ $result->event->name ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $result->sport->name ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $result->division ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $result->category ?? '-' }}</td>
                                        <td class="px-4 py-2 font-bold
                                            {{ $result->medal === 'Gold' ? 'text-yellow-400' :
                                               ($result->medal === 'Silver' ? 'text-gray-300' : 'text-orange-400') }}">
                                            {{ $result->medal }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ 
                                                $result->created_at 
                                                    ? $result->created_at->format('M d, Y') 
                                                    : ($result->event?->award_date?->format('M d, Y') ?? '-') 
                                            }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        </div>

    </div>
</section>
@endsection
