{{-- Desktop Row --}}
<div
    @click='openModal(
        @json(["id"=>$division->id ?? null,"name"=>$division->name]),
        @json($division->results)
    )'
    class="hidden md:grid grid-cols-[40px_1fr_420px_96px]
           items-center p-6 bg-white/10 backdrop-blur-md
           rounded-xl shadow-lg cursor-pointer
           hover:scale-[1.01] hover:shadow-2xl transition
           max-w-6xl mx-auto"
>
    <div class="text-white font-bold text-lg text-center">
        {{ $index + 1 }}
    </div>

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
</div>

{{-- Mobile Card --}}
<div
    @click='openModal(
        @json(["id"=>$division->id ?? null,"name"=>$division->name]),
        @json($division->results)
    )'
    class="md:hidden bg-white/10 backdrop-blur-md
           rounded-xl shadow-lg p-4 space-y-4
           cursor-pointer hover:shadow-xl transition
           max-w-md mx-auto"
>
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
</div>
