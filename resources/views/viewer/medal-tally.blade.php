@extends('layouts.viewer')

@section('page-title', 'Medal Tally')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Medal Tally</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-xl shadow-lg">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">School</th>
                    <th class="py-3 px-6 text-center">Gold</th>
                    <th class="py-3 px-6 text-center">Silver</th>
                    <th class="py-3 px-6 text-center">Bronze</th>
                    <th class="py-3 px-6 text-center">Total</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($medalTally as $index => $schoolMedals)
                <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : '' }}">
                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                    <td class="py-3 px-6">{{ $schools[$schoolMedals->school_id]->name ?? 'Unknown' }}</td>
                    <td class="py-3 px-6 text-center">{{ $schoolMedals->gold }}</td>
                    <td class="py-3 px-6 text-center">{{ $schoolMedals->silver }}</td>
                    <td class="py-3 px-6 text-center">{{ $schoolMedals->bronze }}</td>
                    <td class="py-3 px-6 text-center">{{ $schoolMedals->gold + $schoolMedals->silver + $schoolMedals->bronze }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
