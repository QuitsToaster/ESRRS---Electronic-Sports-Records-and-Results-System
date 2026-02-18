@extends('layouts.app')

@section('page-title', 'Add User')

@section('content')

@php
use App\Models\Division;
$divisions = Division::orderBy('name')->get();
@endphp

<div class="mb-6">
    <p class="text-sm text-gray-600 mt-1">
        Create a user and assign role, division (SDO), and system access.
    </p>
</div>

<div class="bg-white shadow rounded-lg p-6 w-full">

<form
    action="{{ route('users.store') }}"
    method="POST"
    class="space-y-6"
    x-data="{
        allAccess: false,
        toggleAll() {
            this.$refs.modules
                .querySelectorAll('input[type=checkbox]')
                .forEach(cb => cb.checked = this.allAccess);
        }
    }"
>
@csrf

{{-- ================= NAME ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *</label>
    <input type="text" name="name" value="{{ old('name') }}" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- ================= EMAIL ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
    <input type="email" name="email" value="{{ old('email') }}" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- ================= PASSWORD ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Password *</label>
    <input type="password" name="password" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password *</label>
    <input type="password" name="password_confirmation" required
        class="w-full border border-gray-300 rounded px-3 py-2">
</div>

{{-- ================= ROLE ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Role *</label>

    <select name="role_id"
            class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">-- Select Existing Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>

    <p class="text-xs text-gray-500 mt-1">
        Select an existing role OR type a new one below.
    </p>
</div>

{{-- ================= ADD NEW ROLE ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
        Or Add New Role
    </label>
    <input type="text"
           name="new_role"
           value="{{ old('new_role') }}"
           placeholder="e.g. Tournament Secretary"
           class="w-full border border-gray-300 rounded px-3 py-2">

    <p class="text-xs text-gray-500 mt-1">
        If filled, this role will be created and used automatically.
    </p>
</div>

{{-- ================= DIVISION (SDO) ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
        Assigned Division (SDO) *
    </label>
    <select name="division_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
        <option value="">-- Select Division --</option>
        @foreach($divisions as $division)
            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                {{ $division->name }}
            </option>
        @endforeach
    </select>

    <p class="text-xs text-gray-500 mt-1">
        This user will ONLY access results & awards for this SDO.
    </p>
</div>

{{-- ================= ACCESS CONTROL ================= --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Module Access
    </label>

    <label class="flex items-center gap-2 mb-3">
        <input type="checkbox" x-model="allAccess" @change="toggleAll">
        Select All Access
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm" x-ref="modules">
        @php
            $modules = [
                'dashboard' => 'Dashboard',
                'results' => 'Results & Awards',
                'sports' => 'Sports',
                'athletes' => 'Athletes',
                'events' => 'Events',
                'participants' => 'Teams',
                'announcements' => 'Announcements',
            ];
        @endphp

        @foreach($modules as $key => $label)
            <label class="flex items-center gap-2">
                <input type="checkbox"
                       name="access[]"
                       value="{{ $key }}"
                       {{ in_array($key, old('access', [])) ? 'checked' : '' }}>
                {{ $label }}
            </label>
        @endforeach
    </div>

    <p class="text-xs text-gray-500 mt-2">
        Tournament Secretaries and Managers usually only need Results & Sports.
    </p>
</div>

{{-- ================= ACTIONS ================= --}}
<div class="flex justify-end gap-3 pt-4 border-t">
    <a href="{{ route('users.index') }}"
       class="px-4 py-2 rounded border border-gray-400 text-gray-700">
        Cancel
    </a>

    <button type="submit"
            class="px-5 py-2 rounded bg-green-600 text-white font-semibold">
        Create User
    </button>
</div>

</form>
</div>
@endsection
