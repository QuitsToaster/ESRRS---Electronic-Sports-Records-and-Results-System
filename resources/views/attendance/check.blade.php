<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">

{{-- Top Bar --}}
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold tracking-tight">
            CEMS | Attendance Monitoring
        </h1>
        <a href="{{ route('welcome') }}"
           class="text-sm font-medium text-slate-600 hover:text-slate-900">
            ← Back to Home
        </a>
    </div>
</header>

{{-- Main Content --}}
<main class="flex-1 max-w-7xl mx-auto w-full px-6 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Scan Card --}}
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col gap-5">
        <div class="text-center">
            <h2 class="text-lg font-semibold">Scan Attendance</h2>
            <p class="text-sm text-slate-500 mt-1">
                Scan QR code or enter manually
            </p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 text-sm px-4 py-3 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 text-rose-700 text-sm px-4 py-3 rounded-lg text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('attendance.store') }}" method="POST" id="qrForm" class="flex flex-col gap-4">
            @csrf

            <input
                type="text"
                name="qr_code"
                id="qr_code"
                placeholder="Scan QR code"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-center text-lg
                       focus:outline-none focus:ring-2 focus:ring-indigo-500"
                autofocus
                autocomplete="off"
            >

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-medium transition">
                Submit
            </button>
        </form>

        <p class="text-xs text-slate-400 text-center mt-2">
            Ready for next scan automatically
        </p>
    </section>

    {{-- Attendance Table --}}
    <section class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Today's Attendance</h3>
            <span class="text-sm text-slate-500">
                {{ \Carbon\Carbon::today()->format('F d, Y') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-slate-500 border-b">
                    <tr>
                        <th class="text-left py-3 px-2 font-medium">Photo</th>
                        <th class="text-left py-3 px-2 font-medium">Name</th>
                        <th class="text-left py-3 px-2 font-medium">Role</th>
                        <th class="text-left py-3 px-2 font-medium">School</th>
                        <th class="text-left py-3 px-2 font-medium">Time In</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @php
                        use App\Models\Attendance;
                        use Carbon\Carbon;

                        $todayAttendances = Attendance::with(['athlete.school','coach.school'])
                            ->whereDate('created_at', Carbon::today())
                            ->get();
                    @endphp

                    @forelse($todayAttendances as $record)
                        <tr class="hover:bg-slate-50">
                            {{-- PROFILE PHOTO --}}
                            <td class="py-3 px-2">
                                @if($record->athlete)
                                    <img src="{{ $record->athlete->profile_photo
                                        ? asset('storage/'.$record->athlete->profile_photo)
                                        : asset('images/avatar-placeholder.png') }}"
                                        class="w-10 h-10 rounded-full object-cover border"
                                        alt="Profile Photo">
                                @elseif($record->coach)
                                    <img src="{{ $record->coach->profile_photo
                                        ? asset('storage/'.$record->coach->profile_photo)
                                        : asset('images/avatar-placeholder.png') }}"
                                        class="w-10 h-10 rounded-full object-cover border"
                                        alt="Profile Photo">
                                @endif
                            </td>

                            {{-- NAME --}}
                            <td class="py-3 px-2 font-medium">
                                {{ $record->athlete->name ?? $record->coach->name ?? '-' }}
                            </td>

                            {{-- ROLE --}}
                            <td class="py-3 px-2 text-slate-600">
                                {{ $record->athlete ? 'Athlete' : ($record->coach ? 'Coach' : '-') }}
                            </td>

                            {{-- SCHOOL --}}
                            <td class="py-3 px-2 text-slate-600">
                                {{ $record->athlete->school->name ?? $record->coach->school->name ?? '-' }}
                            </td>

                            {{-- TIME IN --}}
                            <td class="py-3 px-2 text-slate-600">
                                {{ $record->time_in }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400">
                                No attendance recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

{{-- MODAL: Already Scanned --}}
@if(session('already_scanned_name'))
<div id="scannedModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-sm text-center">
        <h2 class="text-lg font-semibold text-slate-800 mb-2">
            Duplicate Scan
        </h2>
        <p class="text-sm text-slate-600 mb-6">
            <strong>{{ session('already_scanned_name') }}</strong> has already been scanned today.
        </p>
        <button onclick="closeModal()"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-medium">
            OK
        </button>
    </div>
</div>
@endif

{{-- JS --}}
<script>
    const qrInput = document.getElementById('qr_code');
    const qrForm  = document.getElementById('qrForm');

    function cleanQRCode(value) {
        return value.trim(); // coach QR is just read as is
    }

    qrInput.addEventListener('change', () => {
        qrInput.value = cleanQRCode(qrInput.value);
        qrForm.submit();
    });

    qrInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            qrInput.value = cleanQRCode(qrInput.value);
            qrForm.submit();
        }
    });

    function closeModal() {
        document.getElementById('scannedModal').remove();
        qrInput.value = '';
        qrInput.focus();
    }

    qrInput.focus();
</script>
</body>
</html>
