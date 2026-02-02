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

    {{-- Medal Tally Container --}}
    <div class="relative max-w-7xl mx-auto pt-20 px-6"
         x-data="{
             open: false,
             division: null,
             medals: { gold: [], silver: [], bronze: [] },
             openModal(div, results) {
                 this.division = div;
                 this.medals = {
                     gold: results.filter(r => r.medal === 'Gold'),
                     silver: results.filter(r => r.medal === 'Silver'),
                     bronze: results.filter(r => r.medal === 'Bronze'),
                 };
                 this.open = true;
             }
         }">

        {{-- ================== MEDAL TALLY BY DIVISION ================== --}}
        {{-- Header --}}
        <div class="hidden md:grid grid-cols-[40px_1fr_420px_96px] items-center mb-6 text-white sticky top-0
                    bg-black/20 backdrop-blur-md px-6 py-3 rounded-xl z-10">
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
            <div class="space-y-4 md:space-y-6">
                @foreach($medalTally as $index => $division)
                    {{-- Desktop Row --}}
                    <div
                        @click="openModal(@json($division->only(['id','name'])), @json($division->results))"
                        class="hidden md:grid grid-cols-[40px_1fr_420px_96px]
                               items-center p-6 bg-white/10 backdrop-blur-md
                               rounded-xl shadow-lg cursor-pointer
                               hover:scale-[1.01] hover:shadow-2xl transition">
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

                        <div class="relative text-center">
                            <div class="absolute top-0 bottom-0 left-0 w-[500px] -z-10
                                        bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-500
                                        opacity-90"></div>
                            <div class="relative text-red-700 font-extrabold text-6xl">
                                {{ $division->total }}
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Card --}}
                    <div
                        @click="openModal(@json($division->only(['id','name'])), @json($division->results))"
                        class="md:hidden bg-white/10 backdrop-blur-md
                               rounded-xl shadow-lg p-4 space-y-4
                               cursor-pointer hover:shadow-xl transition">
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
                            <div class="text-yellow-400 text-2xl font-bold">{{ $division->gold }}</div>
                            <div class="text-gray-300 text-2xl font-bold">{{ $division->silver }}</div>
                            <div class="text-orange-400 text-2xl font-bold">{{ $division->bronze }}</div>
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

          {{-- ================= MODAL ================= --}}
    <div
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4"
    >
        <div
            @click.away="open = false"
            class="bg-white rounded-2xl shadow-2xl
                   max-w-4xl w-full max-h-[80vh]
                   overflow-y-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800"
                    x-text="division?.name + ' — Medal Details'"></h3>
                <button @click="open = false" class="text-3xl text-gray-400 hover:text-black">&times;</button>
            </div>

            <template x-for="(list, type) in medals" :key="type">
                <div class="mb-6">
                    <h4 class="text-xl font-bold mb-2"
                        :class="{
                            'text-yellow-600': type === 'gold',
                            'text-gray-600': type === 'silver',
                            'text-orange-600': type === 'bronze'
                        }"
                        x-text="type.toUpperCase()"></h4>

                    <div class="space-y-2">
                        <template x-for="m in list" :key="m.id">
                            <div class="p-3 bg-gray-100 rounded-lg text-sm">
                                <strong x-text="m.event?.name ?? 'Event'"></strong>
                                — <span x-text="m.sport?.name ?? 'Sport'"></span>
                            </div>
                        </template>

                        <p x-show="list.length === 0" class="text-gray-400 italic text-sm">
                            No medals
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>

        {{-- ================== ALL MEDALS AWARDED ================== --}}
        @php
            use App\Models\ResultDivision;
            use App\Models\Sport;

            $allResults = ResultDivision::with(['event','sport'])->latest()->get();
            $allSports = Sport::orderBy('name')->get();
        @endphp

        <div class="mt-16">
            <h2 class="text-center text-6xl md:text-8xl font-extrabold mb-6"
                style="font-family: 'Kristi', cursive;
                       background: linear-gradient(90deg, #FFD700, #FFEA00, #FFC700);
                       -webkit-background-clip: text;
                       -webkit-text-fill-color: transparent;">
                All Medals Awarded
            </h2>

            @foreach(['Elementary','Secondary'] as $level)
                <h3 class="text-3xl font-semibold text-white mb-4">{{ $level }} Division</h3>

                @foreach($allSports as $sport)
                    @php
                        $sportResults = $allResults->where('division', $level)
                                                    ->where('sport_id', $sport->id);
                    @endphp

                    @if($sportResults->isEmpty())
                        <div class="text-gray-400 py-2 mb-2">
                            No results recorded for <strong>{{ $sport->name }}</strong> in {{ $level }} Division.
                        </div>
                    @else
                        <h4 class="text-xl font-semibold text-yellow-300 mb-2">{{ $sport->name }}</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white/10 backdrop-blur-md rounded-xl text-white shadow-lg">
                                <thead class="bg-white/20">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Event</th>
                                        <th class="px-4 py-2 text-left">Category</th>
                                        <th class="px-4 py-2 text-left">Medal</th>
                                        <th class="px-4 py-2 text-left">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sportResults as $result)
                                        <tr class="border-b border-white/20 hover:bg-white/10 transition">
                                            <td class="px-4 py-2">{{ $result->event->name ?? '-' }}</td>
                                            <td class="px-4 py-2">{{ $result->category ?? '-' }}</td>
                                            <td class="px-4 py-2 font-bold
                                                {{ $result->medal === 'Gold' ? 'text-yellow-400' :
                                                   ($result->medal === 'Silver' ? 'text-gray-300' : 'text-orange-400') }}">
                                                {{ $result->medal }}
                                            </td>
                                            <td class="px-4 py-2">
                                                {{ $result->created_at
                                                    ? $result->created_at->format('M d, Y')
                                                    : ($result->event?->award_date?->format('M d, Y') ?? '-') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

    </div>
</section>
@endsection
