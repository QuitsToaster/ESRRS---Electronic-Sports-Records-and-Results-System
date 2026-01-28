@extends('layouts.viewer')

@section('page-title', 'Medal Tally')

@section('content')
<section class="relative py-10 min-h-screen">
    {{-- Background --}}
    <div class="absolute inset-0">
        <img src="{{ asset('storage/medalbg.jpg') }}" 
             alt="Medal Background" 
             class="w-full h-full object-cover brightness-75 blur-sm">
        <div class="absolute inset-0 bg-black/30"></div>
    </div>

    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto px-6">

        {{-- Medal Tally Header --}}
        <div class="grid grid-cols-[40px_1fr_420px_96px] items-center mb-6 text-white">
            <div></div>
            <h2 class="text-3xl font-bold">Medal Tally</h2>

            {{-- Medal Labels --}}
            <div class="grid grid-cols-3 text-center font-semibold text-lg">
                <span class="text-yellow-400">Gold</span>
                <span class="text-gray-300">Silver</span>
                <span class="text-orange-400">Bronze</span>
            </div>

            <div class="text-center font-semibold text-4xl text-red-600">Total</div>
        </div>

        @if($medalTally->isEmpty())
            <p class="text-gray-200 text-sm">No medals awarded yet.</p>
        @else
            <div class="grid gap-4 md:gap-6">
                @foreach($medalTally as $index => $schoolMedals)
                    @php
                        $schoolModel = $schools[$schoolMedals->school_id] ?? null;
                        $total = $schoolMedals->gold + $schoolMedals->silver + $schoolMedals->bronze;
                    @endphp

                    {{-- Row --}}
                    <div class="grid grid-cols-[40px_1fr_420px_70px] items-center p-4 md:p-6 
                                bg-white/10 backdrop-blur-md rounded-xl shadow-lg hover:shadow-2xl transition">

                        {{-- Rank --}}
                        <div class="text-white font-bold text-lg text-center">
                            {{ $index + 1 }}
                        </div>

                        {{-- School Info --}}
                        <div class="flex items-center space-x-4">
                            @if($schoolModel && $schoolModel->logo)
                                <img src="{{ asset('storage/' . $schoolModel->logo) }}"
                                     class="w-12 h-12 object-cover rounded-full border border-white/30">
                            @else
                                <div class="w-12 h-12 bg-white/30 rounded-full flex items-center justify-center text-gray-200 text-xs">
                                    N/A
                                </div>
                            @endif

                            <span class="text-white font-semibold text-lg">
                                {{ $schoolModel->name ?? 'Unknown' }}
                            </span>
                        </div>

                        {{-- Medal Counts --}}
                        <div class="grid grid-cols-3 text-center font-extrabold text-4xl">
                            <span class="text-yellow-400">{{ $schoolMedals->gold }}</span>
                            <span class="text-gray-300">{{ $schoolMedals->silver }}</span>
                            <span class="text-orange-400">{{ $schoolMedals->bronze }}</span>
                        </div>

                        {{-- Total --}}
                        <div class="relative h-full">
                            <div class="absolute top-0 bottom-0 left-0 w-[198px] -z-10
                                        bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>

                            <div class="relative text-center text-red-600 font-extrabold text-6xl py-4">
                                {{ $total }}
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

        {{-- All Medals Awarded Section --}}
        <div class="mt-16">
            <h2 class="text-center text-8xl font-extrabold mb-6"
    style="font-family: 'Kristi', cursive;
           background: linear-gradient(90deg, #FFD700, #FFEA00, #FFC700);
           -webkit-background-clip: text;
           -webkit-text-fill-color: transparent;
           text-shadow: 0 0 10px rgba(255, 215, 0, 0.7), 0 0 20px rgba(255, 223, 0, 0.5);">
    All Medals Awarded
</h2>


            @php
                use App\Models\Result;
                $results = Result::with(['event','sport','school','awardedBy'])
                            ->latest()
                            ->get();
            @endphp

            @foreach(['Elementary','Secondary'] as $division)
                <h3 class="text-4xl font-semibold text-white mb-4">{{ $division }} Division</h3>

                @php
                    $divisionResults = $results->where('division', $division);
                @endphp

                @if($divisionResults->isEmpty())
                    <p class="text-gray-200 mb-6">No medals awarded yet for {{ $division }} division.</p>
                @else
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full bg-white/10 backdrop-blur-md rounded-xl text-white shadow-lg">
                            <thead class="bg-white/20 text-white">
                                <tr>
                                    <th class="px-4 py-2 text-left">Event</th>
                                    <th class="px-4 py-2 text-left">Sport</th>
                                    <th class="px-4 py-2 text-left">School</th>
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
                                        <td class="px-4 py-2">{{ $result->school->name ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $result->category }}</td>
                                        <td class="px-4 py-2">
                                            <span class="@if($result->medal==='Gold') text-yellow-400 font-bold
                                                          @elseif($result->medal==='Silver') text-gray-300 font-bold
                                                          @else text-orange-400 font-bold @endif">
                                                {{ $result->medal }}
                                            </span>
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
