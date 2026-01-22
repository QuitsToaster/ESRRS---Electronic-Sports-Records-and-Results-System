@extends('layouts.app')

@section('page-title', 'Manage Attendance')

@section('content')
<div class="container mx-auto px-4 py-6">

    <h2 class="text-2xl font-bold text-gray-800 mb-4">Attendance Management</h2>
    <p class="text-gray-600 mb-6">This page will allow you to generate QR codes, view participants' attendance, and export attendance as PDF.</p>

    <div class="flex flex-wrap gap-4 mb-6">
        {{-- Generate QR Button --}}
        <form action="{{ route('attendance.generate') }}" method="POST">
            @csrf
            <button type="submit" 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow-lg">
                Generate Attendance QR Code
            </button>
        </form>

        {{-- Export PDF Button --}}
        <form action="{{ route('attendance.export') }}" method="GET" target="_blank">
            <button type="submit" 
                class="px-6 py-2 bg-red-600 hover:bg-red-700  text-white font-semibold rounded shadow-lg">
                Export Attendance (PDF)
            </button>
        </form>
    </div>

    {{-- Attendance Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 text-left">Name</th>
                    <th class="px-2 py-1 text-left">Team</th>
                    <th class="px-2 py-1 text-left">Time In</th>
                </tr>
            </thead>
            <tbody>
                @forelse($athletes as $athlete)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-2 py-1">{{ $athlete->name }}</td>
                        <td class="px-2 py-1">{{ $athlete->school->name ?? 'N/A' }}</td>
                        <td class="px-2 py-1">—</td> {{-- Placeholder for time_in --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-gray-400 px-2 py-2 text-center">No athletes found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
