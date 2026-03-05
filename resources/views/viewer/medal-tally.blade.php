@extends('layouts.viewer')

@section('page-title', 'Medal Tally')

@section('content')
<section class="relative py-10 min-h-screen -mt-16">

    {{-- ================= BACKGROUND ================= --}}
    <div class="absolute inset-0">
        <img src="{{ asset('storage/medalbg.jpg') }}"
             class="w-full h-full object-cover brightness-75 blur-sm">
        <div class="absolute inset-0 bg-black/30"></div>
    </div>

    @php
        use App\Models\AllAwardResult;
        use App\Models\Division;

        /* =========================================================
         | REGULAR MEDAL TALLY (Elementary + Secondary)
         ========================================================= */
        $regularRaw = AllAwardResult::select(
                'sdo',
                'medal',
                \DB::raw('SUM(`count`) as total')
            )
            ->where('status','approved')
            ->whereIn('division',['Elementary','Secondary'])
            ->groupBy('sdo','medal')
            ->get()
            ->groupBy('sdo');

        $medalTallyRegular = Division::orderBy('name')->get()->map(function($division) use ($regularRaw) {
            $rows = $regularRaw->get($division->name, collect());

            $gold   = $rows->where('medal','Gold')->sum('total');
            $silver = $rows->where('medal','Silver')->sum('total');
            $bronze = $rows->where('medal','Bronze')->sum('total');

            return (object)[
                'name'   => $division->name,
                'logo'   => $division->logo,
                'gold'   => $gold,
                'silver' => $silver,
                'bronze' => $bronze,
                'total'  => $gold + $silver + $bronze,
                'results'=> AllAwardResult::with(['sport','event'])
                                ->where('status','approved')
                                ->whereIn('division',['Elementary','Secondary'])
                                ->where('sdo',$division->name)
                                ->get(),
            ];
        })->sort(function($a,$b){
            if($a->gold !== $b->gold) return $b->gold <=> $a->gold;
            if($a->silver !== $b->silver) return $b->silver <=> $a->silver;
            return $b->bronze <=> $a->bronze;
        })->values();

        /* =========================================================
         | PARA GAMES MEDAL TALLY
         ========================================================= */
        $paraRaw = AllAwardResult::select(
                'sdo',
                'medal',
                \DB::raw('SUM(`count`) as total')
            )
            ->where('status','approved')
            ->where('division','Paragames')
            ->groupBy('sdo','medal')
            ->get()
            ->groupBy('sdo');

        $medalTallyPara = Division::orderBy('name')->get()->map(function($division) use ($paraRaw) {
            $rows = $paraRaw->get($division->name, collect());

            $gold   = $rows->where('medal','Gold')->sum('total');
            $silver = $rows->where('medal','Silver')->sum('total');
            $bronze = $rows->where('medal','Bronze')->sum('total');

            return (object)[
                'name'   => $division->name,
                'logo'   => $division->logo,
                'gold'   => $gold,
                'silver' => $silver,
                'bronze' => $bronze,
                'total'  => $gold + $silver + $bronze,
                'results'=> AllAwardResult::with(['sport','event'])
                                ->where('status','approved')
                                ->where('division','Paragames')
                                ->where('sdo',$division->name)
                                ->get(),
            ];
        })->sort(function($a,$b){
            if($a->gold !== $b->gold) return $b->gold <=> $a->gold;
            if($a->silver !== $b->silver) return $b->silver <=> $a->silver;
            return $b->bronze <=> $a->bronze;
        })->values();
    @endphp

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

         {{-- ================= ALL MEDALS AWARDED ================= --}}
        @php
            use App\Models\IndividualAwardResult;
            use App\Models\Sport;

            $individualResults = IndividualAwardResult::with(['sport','athlete','event'])
                ->latest()->get();

            $sports = Sport::orderBy('name')->get();
        @endphp

        <div class="mt-20">
            <h2 class="text-center text-6xl font-extrabold mb-10 text-yellow-400">
                Individual Awards
            </h2>

            @foreach(['Elementary','Secondary','Paragames'] as $division)
                <h3 class="text-3xl font-semibold text-white mb-4">{{ $division }}</h3>

                @foreach($sports as $sport)
                    @php
                        $rows = $individualResults
                            ->where('status', 'approved')
                            ->where('division', $division)
                            ->where('sport_id', $sport->id);
                    @endphp

                    @if($rows->isNotEmpty())
                        <h4 class="text-xl text-yellow-300 mb-2">{{ $sport->name }}</h4>

                        <table class="w-full mb-6 bg-white/10 text-white rounded-xl">
                            <thead class="bg-white/20">
                                <tr>
                                    <!-- <th class="px-4 py-2">Event</th> -->
                                    <th class="px-4 py-2">Athlete</th>
                                    <th class="px-4 py-2">Category</th>
                                    <th class="px-4 py-2">School</th>
                                    <th class="px-4 py-2">Municipality/Town/City</th>
                                    <th class="px-4 py-2">Division</th>
                                    <th class="px-4 py-2">Medal</th>
                                    <th class="px-4 py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $r)
                                    <tr class="border-b border-white/20">
                                        <!-- <td class="px-4 py-2">{{ $r->event?->name }}</td> -->
                                        <td class="px-4 py-2">{{ $r->athlete?->full_name }}</td>
                                        <td class="px-4 py-2">{{ $r->athlete?->category }}</td>
                                        <td class="px-4 py-2">{{ $r->athlete?->school_name }}</td>
                                        <td class="px-4 py-2">{{ $r->athlete?->municipality }}</td>
                                        <td class="px-4 py-2">SDO - {{ $r->athlete?->school_division }}</td>
                                        <td class="px-4 py-2 font-bold
                                            {{ $r->medal==='Gold'?'text-yellow-400':($r->medal==='Silver'?'text-gray-300':'text-orange-400') }}">
                                            {{ $r->medal }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $r->created_at?->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endforeach
            @endforeach
        </div>

        {{-- =========================================================
         | REGULAR MEDAL TALLY HEADER
         ========================================================= --}}
        <div class="hidden md:grid grid-cols-[40px_1fr_420px_96px] items-center mb-6 text-white
                    bg-black/20 backdrop-blur-md px-6 py-3 rounded-xl">
            <div></div>
            <h2 class="text-3xl font-bold">Medal Tally (Regular)</h2>
            <div class="grid grid-cols-3 text-center font-semibold text-lg">
                <span class="text-yellow-400">Gold</span>
                <span class="text-gray-300">Silver</span>
                <span class="text-orange-400">Bronze</span>
            </div>
            <div class="text-center font-bold text-4xl text-red-600">Total</div>
        </div>

        @foreach($medalTallyRegular as $index => $division)
            <div
                @click="openModal({ name: '{{ $division->name }}' }, @json($division->results))"
                class="hidden md:grid grid-cols-[40px_1fr_420px_96px]
                       items-center p-6 bg-white/10 backdrop-blur-md
                       rounded-xl shadow-lg cursor-pointer mb-4">

                <div class="text-white font-bold text-lg text-center">{{ $index + 1 }}</div>

                <div class="flex items-center gap-4">
                    @if($division->logo)
                        <img src="{{ asset('storage/' . $division->logo) }}"
                             class="w-12 h-12 object-cover rounded-full border border-white/30">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center font-bold">
                            {{ strtoupper(substr($division->name,0,2)) }}
                        </div>
                    @endif
                    <span class="text-white font-semibold text-lg">{{ $division->name }}</span>
                </div>

                <div class="grid grid-cols-3 text-center font-extrabold text-4xl">
                    <span class="text-yellow-400">{{ $division->gold }}</span>
                    <span class="text-gray-300">{{ $division->silver }}</span>
                    <span class="text-orange-400">{{ $division->bronze }}</span>
                </div>

                <div class="text-center text-red-700 font-extrabold text-6xl">
                    {{ $division->total }}
                </div>
            </div>
        @endforeach

       {{-- =========================================================
 | PARA GAMES MEDAL TALLY
 ========================================================= --}}
<div class="mt-20 hidden md:grid grid-cols-[40px_1fr_420px_96px] items-center mb-6 text-white
            bg-black/30 backdrop-blur-md px-6 py-3 rounded-xl">
    <div></div>
    <h2 class="text-3xl font-bold">Paragames Medal Tally</h2>
    <div class="grid grid-cols-3 text-center font-semibold text-lg">
        <span class="text-yellow-400">Gold</span>
        <span class="text-gray-300">Silver</span>
        <span class="text-orange-400">Bronze</span>
    </div>
    <div class="text-center font-bold text-4xl text-red-600">Total</div>
</div>

@foreach($medalTallyPara as $index => $division)
    <div
        @click="openModal({ name: '{{ $division->name }}' }, @json($division->results))"
        class="hidden md:grid grid-cols-[40px_1fr_420px_96px]
               items-center p-6 bg-white/10 backdrop-blur-md
               rounded-xl shadow-lg cursor-pointer mb-4">

        {{-- Rank --}}
        <div class="text-white font-bold text-lg text-center">
            {{ $index + 1 }}
        </div>

        {{-- Division Logo + Name --}}
        <div class="flex items-center gap-4">
            @if($division->logo)
                <img
                    src="{{ asset('storage/' . $division->logo) }}"
                    class="w-12 h-12 object-cover rounded-full border border-white/30"
                    alt="{{ $division->name }}"
                >
            @else
                <div class="w-12 h-12 rounded-full bg-gray-300
                            flex items-center justify-center
                            text-gray-700 font-bold">
                    {{ strtoupper(substr($division->name ?? 'NA', 0, 2)) }}
                </div>
            @endif

            <span class="text-white font-semibold text-lg">
                {{ $division->name }}
            </span>
        </div>

        {{-- Medal Counts --}}
        <div class="grid grid-cols-3 text-center font-extrabold text-4xl">
            <span class="text-yellow-400">{{ $division->gold }}</span>
            <span class="text-gray-300">{{ $division->silver }}</span>
            <span class="text-orange-400">{{ $division->bronze }}</span>
        </div>

        {{-- Total --}}
        <div class="text-center text-red-700 font-extrabold text-6xl">
            {{ $division->total }}
        </div>
    </div>
@endforeach


        {{-- ================= MODAL ================= --}}
<div
    x-cloak
    x-show="open"
    x-transition.opacity
    @keydown.escape.window="open=false"
    class="fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center px-4">

    <div
        @click.stop
        class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-y-auto p-6">

        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="text-2xl font-bold"
                x-text="division?.name + ' — Medal Details'"></h3>
            <button @click="open=false" class="text-3xl">&times;</button>
        </div>

        <template x-for="(list,type) in medals" :key="type">
            <div class="mb-6">
                <h4 class="text-xl font-bold mb-2" x-text="type.toUpperCase()"></h4>

                <template x-for="m in list" :key="m.id">
                    <div class="p-3 bg-gray-100 rounded-lg text-sm mb-2">
                        <strong x-text="m.event?.name ?? 'Event'"></strong>
                        — <span x-text="m.sport?.name ?? 'Sport'"></span>
                    </div>
                </template>

                <p x-show="list.length === 0" class="italic text-gray-400">No medals</p>
            </div>
        </template>
    </div>
</div>

    </div>
</section>
@endsection
