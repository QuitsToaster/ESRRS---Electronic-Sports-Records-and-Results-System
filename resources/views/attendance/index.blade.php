@extends('layouts.app')

@section('page-title', 'Manage Attendance')

@section('content')

<div class="container mx-auto px-4 py-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">
            Attendance Management
        </h2>

        <form action="{{ route('attendance.export') }}" method="GET" target="_blank">
            <button type="submit"
                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded shadow-lg">
                Export Attendance (PDF)
            </button>
        </form>
    </div>

    <p class="text-gray-600 mb-6">
        View and monitor athletes' and coaches' attendance.
    </p>

    {{-- ================= DIVISION FILTER ================= --}}
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <form method="GET" action="{{ route('attendance.index') }}">
            <div class="flex flex-col md:flex-row md:items-end gap-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Filter by Division (SDO)
                    </label>
                    <select name="division"
                        onchange="this.form.submit()"
                        class="border border-gray-300 rounded px-3 py-2 w-64">
                        
                        <option value="">All Divisions</option>

                        @foreach($divisions as $division)
                            <option value="{{ $division }}"
                                {{ $selectedDivision == $division ? 'selected' : '' }}>
                                SDO - {{ $division }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($selectedDivision)
                    <div>
                        <a href="{{ route('attendance.index') }}"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">
                            Clear Filter
                        </a>
                    </div>
                @endif

            </div>
        </form>
    </div>

    {{-- ================= ATTENDED TODAY ================= --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">
            Attendance Today
            @if($selectedDivision)
                - SDO {{ $selectedDivision }}
            @endif
        </h3>

        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-left">Role</th>
                        <th class="px-2 py-1 text-left">Division</th>
                        <th class="px-2 py-1 text-left">Time In</th>
                        <th class="px-2 py-1 text-left">Time Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attended as $record)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1">
                                {{ $record->athlete->full_name ?? $record->coach->name ?? '-' }}
                            </td>
                            <td class="px-2 py-1">
                                {{ $record->athlete ? 'Athlete' : 'Coach' }}
                            </td>
                            <td class="px-2 py-1">
                                SDO - {{ $record->athlete->school_division ?? $record->coach->school->division ?? 'N/A' }}
                            </td>
                            <td class="px-2 py-1">{{ $record->time_in ?? '-' }}</td>
                            <td class="px-2 py-1">{{ $record->time_out ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-3">
                                No attendance recorded
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= NOT ATTENDED ATHLETES ================= --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">
            Athletes without Attendance Today
        </h3>

        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-left">Division</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notAttended as $athlete)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $athlete->full_name }}</td>
                            <td class="px-2 py-1">
                                SDO - {{ $athlete->school_division ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-400 py-3">
                                All athletes have attendance today
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= NOT ATTENDED COACHES ================= --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">
            Coaches without Attendance Today
        </h3>

        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-left">Team</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notAttendedCoaches as $coach)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $coach->name }}</td>
                            <td class="px-2 py-1">{{ $coach->school->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-400 py-3">
                                All coaches have attendance today
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection