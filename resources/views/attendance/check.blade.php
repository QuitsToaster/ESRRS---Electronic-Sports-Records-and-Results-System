<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Scanner - ESRRS</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
.card { background: #ffffffcc; backdrop-filter: blur(8px); border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.success-pulse { animation: pulse-green 2s infinite; }
.error-pulse { animation: pulse-red 2s infinite; }

@keyframes pulse-green {
  0%{box-shadow:0 0 0 0 rgba(34,197,94,.5);}
  70%{box-shadow:0 0 0 15px rgba(34,197,94,0);}
}
@keyframes pulse-red {
  0%{box-shadow:0 0 0 0 rgba(239,68,68,.5);}
  70%{box-shadow:0 0 0 15px rgba(239,68,68,0);}
}
</style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-6">

{{-- ================= INSTRUCTIONS ================= --}}
<div class="w-full max-w-5xl mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm">
    <h2 class="font-semibold mb-2 text-lg">How to Use the Attendance Scanner</h2>
    <ul class="list-disc list-inside space-y-1 text-sm">
        <li>Use your <strong>QR code</strong> to record Time In / Time Out.</li>
        <li>Choose <strong>External Scanner</strong> if using a physical QR scanner or <strong>Use Camera</strong> to scan via your device camera.</li>
        <li>If using camera scanning, ensure your connection is <strong>HTTPS</strong> for security and browser access.</li>
        <li>After scanning, the attendance will be automatically submitted and confirmed on screen.</li>
        <li>Recent attendance records are displayed on the right for verification.</li>
    </ul>
</div>


<div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-6">

{{-- ================= SCANNER ================= --}}
<div class="card p-8 flex flex-col items-center">
    <h1 class="text-2xl font-bold mb-2">ESRRS Attendance</h1>
    <p class="text-gray-600 mb-6 text-center">Scan your QR code to Time In / Time Out</p>

    {{-- STATUS --}}
    @if(session('success'))
    <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded success-pulse text-center w-full">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-3 bg-rose-50 text-rose-800 rounded error-pulse text-center w-full">{{ session('error') }}</div>
    @endif

    {{-- HTTPS WARNING --}}
    <div id="httpsWarning" class="hidden mb-4 p-2 bg-yellow-50 text-yellow-800 rounded text-sm text-center w-full">
        ⚠ Camera scanning requires <strong>HTTPS</strong>.
    </div>

    {{-- MODE SWITCH --}}
    <div class="flex gap-3 mb-4">
        <button id="scannerMode" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">External Scanner</button>
        <button id="cameraMode" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition">Use Camera</button>
    </div>

    {{-- FORM --}}
    <form action="{{ route('attendance.store') }}" method="POST" id="qrForm" class="w-full">
        @csrf
        <input
            type="text"
            name="qr_code"
            id="qr_code"
            placeholder="Scan QR code..."
            class="w-full p-4 text-xl text-center border-2 border-dashed border-indigo-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400"
            autofocus
        >
        <button type="submit" class="hidden"></button>
    </form>

    {{-- CAMERA VIEW --}}
    <div id="cameraBox" class="hidden mt-4 w-full max-w-sm">
        <div id="qr-reader" class="w-full"></div>
    </div>
</div>

{{-- ================= RECENT ATTENDANCE ================= --}}
<div class="card p-6 overflow-y-auto max-h-[80vh]">
    <h2 class="text-xl font-semibold mb-4 text-center">Today’s Attendance</h2>

    @php
    use App\Models\Attendance;
    use Carbon\Carbon;

    $recent = Attendance::with(['athlete','coach'])
        ->whereDate('created_at', Carbon::today())
        ->latest()
        ->limit(15)
        ->get();
    @endphp

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 font-medium">Name</th>
                    <th class="px-3 py-2 font-medium">School</th>
                    <th class="px-3 py-2 font-medium">Region</th>
                    <th class="px-3 py-2 font-medium">Time In</th>
                    <th class="px-3 py-2 font-medium">Time Out</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($recent as $r)
                @php
                    $user = $r->athlete ?? $r->coach;
                    $name = $user->name ?? trim($user->first_name.' '.$user->last_name);
                    $region = $user->region ?? '—';
                    $school = $user->school_name ?? '—';
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-3 py-2 font-medium">{{ $name }}</td>
                    <td class="px-3 py-2">{{ $school }}</td>
                    <td class="px-3 py-2">{{ $region }}</td>
                    <td class="px-3 py-2">{{ $r->time_in ? Carbon::parse($r->time_in)->format('h:i A') : '—' }}</td>
                    <td class="px-3 py-2">{{ $r->time_out ? Carbon::parse($r->time_out)->format('h:i A') : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-gray-400">No attendance records today</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
function submitQR() {
    const value = qrInput.value.trim();
    if (value) qrForm.submit();
}

qrInput.addEventListener('input', submitQR);
qrInput.addEventListener('keydown', e => { if(e.key==='Enter'){e.preventDefault();submitQR();} });
qrInput.addEventListener('change', submitQR);

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
        decodedText => {
            qrInput.value = decodedText.trim();
            stopCamera();
            qrForm.submit();
        },
        () => {}
    ).catch(() => alert("Camera access failed. Please allow permission or use HTTPS."));
}

function stopCamera() {
    if (qrScanner) qrScanner.stop().then(() => qrScanner.clear());
    qrScanner = null;
}
</script>

</body>
</html>
