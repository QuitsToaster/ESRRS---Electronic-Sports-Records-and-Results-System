@extends('layouts.app')

@section('page-title', 'Manage Attendance')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header with Export PDF --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">Attendance Management</h2>

        <form action="{{ route('attendance.export') }}" method="GET" target="_blank">
            <button type="submit"
                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded shadow-lg">
                Export Attendance (PDF)
            </button>
        </form>
    </div>

    <p class="text-gray-600 mb-6">
        View and monitor athletes' and coaches' attendance, generate QR codes, and export records as PDF.
    </p>

    {{-- Attended --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Attendance Today</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-left">Role</th>
                        <th class="px-2 py-1 text-left">Team</th>
                        <th class="px-2 py-1 text-left">Time In</th>
                        <th class="px-2 py-1 text-left">Time Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attended as $record)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-2 py-1">
                                {{ $record->athlete->full_name ?? $record->coach->name ?? '-' }}
                            </td>
                            <td class="px-2 py-1">
                                {{ $record->athlete ? 'Athlete' : ($record->coach ? 'Coach' : '-') }}
                            </td>
                            <td class="px-2 py-1">
                                {{ $record->athlete->school->name ?? $record->coach->school->name ?? 'N/A' }}
                            </td>
                            <td class="px-2 py-1">
                                {{ $record->time_in ?? '-' }}
                            </td>
                            <td class="px-2 py-1">
                                {{ $record->time_out ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-gray-400 px-2 py-2 text-center">
                                No attendance recorded today
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Not Attended Athletes --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Athletes without Attendance Today</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-left">Team</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notAttended as $athlete)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $athlete->full_name }}</td>
                            <td class="px-2 py-1">{{ $athlete->school->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-gray-400 px-2 py-2 text-center">
                                All athletes have recorded attendance today
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Not Attended Coaches --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Coaches without Attendance Today</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-left">Team</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notAttendedCoaches as $coach)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $coach->name }}</td>
                            <td class="px-2 py-1">{{ $coach->school->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-gray-400 px-2 py-2 text-center">
                                All coaches have recorded attendance today
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
