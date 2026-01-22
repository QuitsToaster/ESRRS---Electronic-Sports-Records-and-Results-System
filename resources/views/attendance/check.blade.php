<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- Back Button --}}
<div class="p-4">
    <a href="{{ route('welcome') }}" 
       class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded shadow">
        &larr; Back
    </a>
</div>


    {{-- Main Content --}}
    <div class="flex-1 flex flex-col md:flex-row gap-6 px-4 md:px-12 py-6">

        {{-- Left Side: QR Code Input --}}
        <div class="md:w-1/3 bg-white rounded-lg shadow p-6 flex flex-col gap-4">
            <h2 class="text-2xl font-bold mb-2 text-center">Attendance Check</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-2 text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-3 rounded mb-2 text-center">
                    {{ session('error') }}
                </div>
            @endif

            <p class="text-gray-600 mb-2 text-center">
                Scan your QR code or enter it manually
            </p>

            <form action="{{ route('attendance.store') }}" method="POST" class="flex flex-col gap-3" id="qrForm">
                @csrf
                <input type="text" name="qr_code" id="qr_code"
                       placeholder="Enter or scan QR code"
                       class="border rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500 text-center text-lg"
                       autofocus autocomplete="off">

                <button type="submit"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded shadow font-semibold">
                    Submit
                </button>
            </form>
        </div>

        {{-- Right Side: Today's Attendance --}}
        <div class="md:w-2/3 bg-white rounded-lg shadow p-6 overflow-x-auto">
            <h3 class="text-xl font-semibold mb-4 text-center">Today's Attendance</h3>
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Athlete</th>
                        <th class="border px-2 py-1">School</th>
                        <th class="border px-2 py-1">Time In</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $todayAttendances = \App\Models\Attendance::with('athlete.school')
                            ->whereDate('created_at', \Carbon\Carbon::today())
                            ->get();
                    @endphp

                    @forelse($todayAttendances as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-1">{{ $record->athlete->name }}</td>
                            <td class="border px-2 py-1">{{ $record->athlete->school->name ?? '-' }}</td>
                            <td class="border px-2 py-1">{{ $record->time_in ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No attendance records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- JS to auto-submit form when Enter is pressed --}}
    <script>
        const qrInput = document.getElementById('qr_code');
        const qrForm = document.getElementById('qrForm');

        qrInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // prevent default Enter behavior
                qrForm.submit();    // submit the form automatically
            }
        });

        // Optional: autofocus the input after submit
        qrInput.focus();
    </script>

</body>
</html>
