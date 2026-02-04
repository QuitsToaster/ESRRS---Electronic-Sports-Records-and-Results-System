<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Scanner - CEMS</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
body { min-height: 100vh; font-family: 'Inter', sans-serif; }
.card { background: rgba(255,255,255,.85); backdrop-filter: blur(8px); }
.success-pulse { animation: pulse-green 2s infinite; }
.error-pulse { animation: pulse-red 2s infinite; }

@keyframes pulse-green {
  0%{box-shadow:0 0 0 0 rgba(34,197,94,.5)}
  70%{box-shadow:0 0 0 15px rgba(34,197,94,0)}
}
@keyframes pulse-red {
  0%{box-shadow:0 0 0 0 rgba(239,68,68,.5)}
  70%{box-shadow:0 0 0 15px rgba(239,68,68,0)}
}
</style>
</head>

<body class="flex items-center justify-center bg-gray-100 p-4">

<div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-6">

{{-- ================= SCANNER ================= --}}
<div class="card rounded-xl p-8 shadow flex flex-col">

<h1 class="text-2xl font-bold text-center">CEMS Attendance</h1>
<p class="text-center text-gray-600 mb-4">Scan QR to Time In / Time Out</p>

{{-- STATUS --}}
@if(session('success'))
<div class="mb-3 p-3 bg-emerald-50 border border-emerald-200 rounded success-pulse text-emerald-800">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-3 p-3 bg-rose-50 border border-rose-200 rounded error-pulse text-rose-800">
    {{ session('error') }}
</div>
@endif

{{-- HTTPS WARNING --}}
<div id="httpsWarning" class="hidden mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded text-sm text-center">
    ⚠ Camera scanning requires <strong>HTTPS</strong>.<br>
    Please use an external QR scanner or access this page via a secure domain.
</div>

{{-- MODE SWITCH --}}
<div class="flex justify-center gap-3 mb-4">
    <button id="scannerMode" class="px-4 py-2 rounded bg-indigo-600 text-white">
        External Scanner
    </button>
    <button id="cameraMode" class="px-4 py-2 rounded bg-gray-200">
        Use Camera
    </button>
</div>

{{-- FORM --}}
<form action="{{ route('attendance.store') }}" method="POST" id="qrForm">
@csrf
<input
    type="text"
    name="qr_code"
    id="qr_code"
    placeholder="Scan QR code..."
    class="w-full p-4 text-xl text-center border-2 border-dashed border-indigo-300 rounded focus:outline-none"
    autofocus
>
<button type="submit" class="hidden"></button>
</form>

{{-- CAMERA VIEW --}}
<div id="cameraBox" class="hidden mt-4">
    <div id="qr-reader" class="w-full max-w-sm mx-auto"></div>
</div>

</div>

{{-- ================= RECENT ATTENDANCE ================= --}}
<div class="card rounded-xl p-6 shadow overflow-y-auto max-h-[80vh]">

<h2 class="text-xl font-bold mb-4 text-center">Today’s Attendance</h2>

@php
use App\Models\Attendance;
use Carbon\Carbon;

$recent = Attendance::with(['athlete','coach'])
    ->whereDate('created_at', Carbon::today())
    ->latest()
    ->limit(15)
    ->get();
@endphp

<table class="w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="p-2 text-left">Name</th>
    <th class="p-2 text-left">Time In</th>
    <th class="p-2 text-left">Time Out</th>
</tr>
</thead>

<tbody>
@forelse($recent as $r)
@php
$user = $r->athlete ?? $r->coach;
$name = $user->name ?? trim($user->first_name.' '.$user->last_name);
@endphp

<tr class="border-b hover:bg-gray-50">
    <td class="p-2 font-medium">{{ $name }}</td>
    <td class="p-2">
        {{ $r->time_in ? Carbon::parse($r->time_in)->format('h:i A') : '—' }}
    </td>
    <td class="p-2">
        {{ $r->time_out ? Carbon::parse($r->time_out)->format('h:i A') : '—' }}
    </td>
</tr>

@empty
<tr>
    <td colspan="3" class="p-4 text-center text-gray-400">No attendance records today</td>
</tr>
@endforelse
</tbody>
</table>

</div>
</div>

<script>
const qrInput = document.getElementById('qr_code');
const qrForm = document.getElementById('qrForm');
const scannerBtn = document.getElementById('scannerMode');
const cameraBtn = document.getElementById('cameraMode');
const cameraBox = document.getElementById('cameraBox');
const httpsWarning = document.getElementById('httpsWarning');

let qrScanner;

/* Detect secure context */
const isSecure =
    location.protocol === 'https:' ||
    location.hostname === 'localhost' ||
    location.hostname === '127.0.0.1';

/* Disable camera if not secure */
if (!isSecure) {
    cameraBtn.disabled = true;
    cameraBtn.classList.add('opacity-50', 'cursor-not-allowed');
    httpsWarning.classList.remove('hidden');
}

/* External Scanner */
qrInput.addEventListener('input', () => {
    if (qrInput.value.trim()) {
        setTimeout(() => qrForm.submit(), 150);
    }
});

/* Camera Scanner */
cameraBtn.onclick = () => {
    if (!isSecure) return;

    cameraBox.classList.remove('hidden');
    qrInput.blur();
    startCamera();
};

scannerBtn.onclick = () => {
    stopCamera();
    cameraBox.classList.add('hidden');
    qrInput.focus();
};

function startCamera() {
    qrScanner = new Html5Qrcode("qr-reader");

    qrScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (decodedText) => {
            qrInput.value = decodedText.trim();
            stopCamera();
            qrForm.submit();
        },
        () => {}
    ).catch(err => {
        alert("Camera access failed. Please allow permission or use HTTPS.");
    });
}

function stopCamera() {
    if (qrScanner) {
        qrScanner.stop().then(() => qrScanner.clear());
        qrScanner = null;
    }
}
</script>

</body>
</html>
