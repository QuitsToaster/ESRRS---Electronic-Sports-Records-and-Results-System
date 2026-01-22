<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check Attendance</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-start py-8">

    <div class="max-w-7xl w-full px-4 flex flex-col space-y-6">

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Check Attendance</h1>
            <p class="text-gray-600 mt-1">Scan the QR code provided by the event organizer to record your attendance.</p>
        </div>

        {{-- Main Content: Two Columns --}}
        <div class="flex flex-col md:flex-row gap-6">

            {{-- Left: QR Scan Input --}}
            <div class="md:w-1/2 bg-white rounded-lg shadow-lg p-6 flex flex-col items-center">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Scan QR Code</h2>

                <input type="text" id="qr-input" placeholder="Scan QR code here..." autofocus
                       class="w-full border border-gray-300 rounded px-4 py-2 text-center focus:outline-none focus:ring focus:ring-blue-200 focus:border-blue-500 text-lg">

                <p id="qr-message" class="mt-4 font-semibold text-center"></p>
            </div>

            {{-- Right: Attendance List --}}
            <div class="md:w-1/2 bg-white rounded-lg shadow-lg p-6 overflow-auto max-h-[600px]">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Participants Checked In</h2>

                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1 text-left">Name</th>
                            <th class="px-2 py-1 text-left">School</th>
                            <th class="px-2 py-1 text-left">Time In</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-list">
                        {{-- Populated dynamically via JS/AJAX --}}
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Alpine.js --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('qr-input');
            const message = document.getElementById('qr-message');
            const attendanceList = document.getElementById('attendance-list');

            input.addEventListener('change', function() {
                const code = input.value.trim();
                if(!code) return;

                fetch("{{ route('viewer.attendance.scan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code })
                })
                .then(res => res.json())
                .then(data => {
                    message.textContent = data.message;
                    message.className = `mt-4 font-semibold text-center ${data.success ? 'text-green-600' : 'text-red-600'}`;

                    if(data.success && data.participant) {
                        const tr = document.createElement('tr');
                        tr.className = "border-b border-gray-200 hover:bg-gray-50";
                        tr.innerHTML = `
                            <td class="px-2 py-1">${data.participant.name}</td>
                            <td class="px-2 py-1">${data.participant.school}</td>
                            <td class="px-2 py-1">${data.participant.time_in}</td>
                        `;
                        attendanceList.prepend(tr);
                    }

                    input.value = ''; // reset input
                    input.focus();    // focus again for next scan
                })
                .catch(err => {
                    console.error(err);
                    message.textContent = "Error checking attendance.";
                    message.className = "mt-4 font-semibold text-center text-red-600";
                    input.value = '';
                    input.focus();
                });
            });
        });
    </script>
</body>
</html>
