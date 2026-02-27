@extends('layouts.app')

@section('page-title', 'Audit Logs')

@section('content')

@php
    $user = auth()->user();
@endphp

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-600 mt-1">
            List of logged actions performed by users in the system
        </p>
    </div>

    @if($user->role->name === 'Super Admin')
        <a href="{{ route('audit-logs.export', request()->query()) }}"
           class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded shadow">
            Export Logs (PDF)
        </a>
    @endif
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- 🔍 FILTER SECTION --}}
<form method="GET" action="{{ route('audit-logs.index') }}"
      class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">

    {{-- Search --}}
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search user, action, model..."
           class="border rounded px-3 py-2 focus:ring focus:ring-blue-200">

    {{-- Start Date --}}
    <input type="date" name="start_date" value="{{ request('start_date') }}"
           class="border rounded px-3 py-2 focus:ring focus:ring-blue-200">

    {{-- End Date --}}
    <input type="date" name="end_date" value="{{ request('end_date') }}"
           class="border rounded px-3 py-2 focus:ring focus:ring-blue-200">

    {{-- Role Filter --}}
    <select name="role_id"
            class="border rounded px-3 py-2 focus:ring focus:ring-blue-200">
        <option value="">All Roles</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}"
                {{ request('role_id') == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>

    {{-- Buttons --}}
    <div class="flex gap-2">
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">
            Filter
        </button>

        @if(request()->hasAny(['search','start_date','end_date','role_id']))
            <a href="{{ route('audit-logs.index') }}"
               class="text-gray-500 hover:text-gray-700 self-center">
                Clear
            </a>
        @endif
    </div>
</form>

{{-- TABLE --}}
<div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
    @if($logs->isEmpty())
        <div class="text-center text-gray-500 py-6">
            No audit logs found.
        </div>
    @else
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">User</th>
                    <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-left">Action</th>
                    <th class="px-4 py-2 text-left">Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    @php
                        $logRole = $log->user->role->name ?? null;
                    @endphp

                    @if(
                        $user->role->name === 'Super Admin' ||
                        ($user->role->name === 'Tournament Secretary' && $logRole === 'Tournament Secretary') ||
                        ($user->role->name === 'Tournament Manager' && $logRole === 'Tournament Manager')
                    )
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">
                                {{ $log->user->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $logRole ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-gray-700 max-w-xl">
    <div class="font-medium">
        {{ $log->action }}
    </div>

    @if($log->description)
        @php
            $desc = json_decode($log->description, true);
        @endphp

        <div class="text-xs text-gray-500 mt-1">
            By: {{ $desc['approved_by'] ?? $desc['performed_by'] ?? 'N/A' }}
            • Role: {{ $desc['role'] ?? 'N/A' }}
            • IP: {{ $desc['ip_address'] ?? 'N/A' }}
        </div>
    @endif
</td>
                            <td class="px-4 py-2">
                                {{ $log->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection