@extends('layouts.app')

@section('page-title', 'Official Results')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12 gap-6">
    <div class="text-center md:text-left">
        <h1 class="text-5xl font-extrabold text-gray-900 tracking-wide">
            OFFICIAL RESULT
        </h1>
        <p class="text-gray-500 mt-2">
            Medal Tally & Division Summary
        </p>
    </div>

    {{-- ADD RESULT BUTTON --}}
    <div class="text-center md:text-right">
        <a href="{{ route('results_divisions.create') }}"
        class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow hover:bg-green-700 transition">
            ➕ Add Result
        </a>
    </div>
</div>


@php
    use App\Models\Division;

    $allDivisions = Division::orderBy('name')->get();

    function medalCount($tally, $division, $level, $gender, $medal) {
        $record = $tally->first(function ($item) use ($division, $level, $gender) {
            return $item->name === $division
                && $item->level === $level
                && $item->gender === $gender;
        });

        return $record ? ($record->{strtolower($medal)} ?? 0) : 0;
    }
@endphp


{{-- ================= OVERALL SUMMARY WITH LOGO ================= --}}
@php
$overall = $allDivisions->map(function ($division) use ($medalTally) {

    $division->total_gold   = $medalTally->where('name',$division->name)->sum('gold');
    $division->total_silver = $medalTally->where('name',$division->name)->sum('silver');
    $division->total_bronze = $medalTally->where('name',$division->name)->sum('bronze');
    $division->total_points = ($division->total_gold * 3)
                            + ($division->total_silver * 2)
                            + ($division->total_bronze * 1);

    return $division;

})->sortByDesc('total_gold')->values();
@endphp

<div class="mb-20">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 border-l-8 border-green-600 pl-4">
        Over-All Summary
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($overall as $index => $division)
            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl shadow-lg p-6 border border-green-200">

                <div class="flex items-center gap-4 mb-4">
                    @if($division->logo)
                        <img src="{{ asset('storage/'.$division->logo) }}"
                             class="w-16 h-16 rounded-full object-cover shadow">
                    @else
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg">
                            {{ strtoupper(substr($division->name,0,2)) }}
                        </div>
                    @endif

                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ $division->name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Overall Rank #{{ $index + 1 }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-between text-center mt-4">
                    <div>
                        <div class="w-6 h-6 bg-yellow-400 rounded-full mx-auto mb-1"></div>
                        <p class="font-bold text-lg">{{ $division->total_gold }}</p>
                        <p class="text-xs text-gray-500">Gold</p>
                    </div>
                    <div>
                        <div class="w-6 h-6 bg-gray-400 rounded-full mx-auto mb-1"></div>
                        <p class="font-bold text-lg">{{ $division->total_silver }}</p>
                        <p class="text-xs text-gray-500">Silver</p>
                    </div>
                    <div>
                        <div class="w-6 h-6 bg-orange-500 rounded-full mx-auto mb-1"></div>
                        <p class="font-bold text-lg">{{ $division->total_bronze }}</p>
                        <p class="text-xs text-gray-500">Bronze</p>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-500">Total Points</span>
                    <p class="text-xl font-bold text-green-600">
                        {{ $division->total_points }}
                    </p>
                </div>

            </div>
        @endforeach
    </div>
</div>

{{-- ================= ELEMENTARY ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-blue-600 pl-4">
        Elementary Division Medal Summary
    </h2>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead>
                <tr class="bg-gray-900 text-white uppercase text-sm">
                    <th rowspan="2" class="px-4 py-3 text-left">Division</th>
                    <th colspan="3" class="bg-blue-700">Boys</th>
                    <th colspan="3" class="bg-pink-600">Girls</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700">
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDivisions as $division)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-left font-semibold">
                            {{ $division->name }}
                        </td>

                        <td class="font-bold text-yellow-500">
                            {{ medalCount($medalTally,$division->name,'Elementary','Boys','Gold') }}
                        </td>
                        <td class="font-bold text-gray-500">
                            {{ medalCount($medalTally,$division->name,'Elementary','Boys','Silver') }}
                        </td>
                        <td class="font-bold text-orange-500">
                            {{ medalCount($medalTally,$division->name,'Elementary','Boys','Bronze') }}
                        </td>

                        <td class="font-bold text-yellow-500">
                            {{ medalCount($medalTally,$division->name,'Elementary','Girls','Gold') }}
                        </td>
                        <td class="font-bold text-gray-500">
                            {{ medalCount($medalTally,$division->name,'Elementary','Girls','Silver') }}
                        </td>
                        <td class="font-bold text-orange-500">
                            {{ medalCount($medalTally,$division->name,'Elementary','Girls','Bronze') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



{{-- ================= SECONDARY ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-blue-600 pl-4">
        Secondary Division Medal Summary
    </h2>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead>
                <tr class="bg-gray-900 text-white uppercase text-sm">
                    <th rowspan="2" class="px-4 py-3 text-left">Division</th>
                    <th colspan="3" class="bg-blue-700">Boys</th>
                    <th colspan="3" class="bg-pink-600">Girls</th>
                </tr>
                <tr class="bg-gray-100 text-gray-700">
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                    <th>Gold</th><th>Silver</th><th>Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDivisions as $division)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-left font-semibold">
                            {{ $division->name }}
                        </td>

                        <td class="font-bold text-yellow-500">
                            {{ medalCount($medalTally,$division->name,'Secondary','Boys','Gold') }}
                        </td>
                        <td class="font-bold text-gray-500">
                            {{ medalCount($medalTally,$division->name,'Secondary','Boys','Silver') }}
                        </td>
                        <td class="font-bold text-orange-500">
                            {{ medalCount($medalTally,$division->name,'Secondary','Boys','Bronze') }}
                        </td>

                        <td class="font-bold text-yellow-500">
                            {{ medalCount($medalTally,$division->name,'Secondary','Girls','Gold') }}
                        </td>
                        <td class="font-bold text-gray-500">
                            {{ medalCount($medalTally,$division->name,'Secondary','Girls','Silver') }}
                        </td>
                        <td class="font-bold text-orange-500">
                            {{ medalCount($medalTally,$division->name,'Secondary','Girls','Bronze') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



{{-- ================= PARA GAMES ================= --}}
<div class="mb-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-8 border-blue-600 pl-4">
        Para Games Division Medal Summary
    </h2>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200 overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead class="bg-purple-700 text-white uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Division</th>
                    <th>Gold</th>
                    <th>Silver</th>
                    <th>Bronze</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allDivisions as $division)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-left font-semibold">
                            {{ $division->name }}
                        </td>

                        <td class="font-bold text-yellow-500">
                            {{ $medalTally->where('name',$division->name)->where('level','Para Games')->sum('gold') }}
                        </td>
                        <td class="font-bold text-gray-500">
                            {{ $medalTally->where('name',$division->name)->where('level','Para Games')->sum('silver') }}
                        </td>
                        <td class="font-bold text-orange-500">
                            {{ $medalTally->where('name',$division->name)->where('level','Para Games')->sum('bronze') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
