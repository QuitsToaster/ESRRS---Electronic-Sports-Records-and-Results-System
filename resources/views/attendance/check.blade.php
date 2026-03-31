<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Scanner · ESRRS</title>
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body { 
            background: #f8fafc;
        }
        
        /* Simple card style */
        .modern-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            transition: all 0.2s ease;
            border: 1px solid #f1f5f9;
        }
        
        .modern-card:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.05);
        }
        
        /* Simple input */
        .simple-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.15s ease;
            background: #f8fafc;
        }
        
        .simple-input:focus {
            border-color: #3b82f6;
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
        }
        
        /* Simple buttons */
        .mode-btn {
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.15s ease;
            background: white;
            border: 1px solid #e2e8f0;
            color: #475569;
        }
        
        .mode-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .mode-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }
        
        /* Status messages */
        .status-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            border-radius: 12px;
            padding: 1rem;
        }
        
        .status-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 1rem;
        }
        
        /* Simple table */
        .simple-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .simple-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            color: #64748b;
            font-weight: 500;
            font-size: 0.875rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .simple-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        .simple-table tr:last-child td {
            border-bottom: none;
        }
        
        .simple-table tr:hover td {
            background: #f8fafc;
        }
        
        /* Badge styles */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-green {
            background: #e6f7e6;
            color: #0e7b0e;
        }
        
        .badge-orange {
            background: #fff4e6;
            color: #b45b0a;
        }
        
        /* Instructions */
        .instructions {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            border-radius: 12px;
            padding: 1rem 1.5rem;
        }
        
        /* Scanner box */
        #qr-reader {
            border: 2px solid #e2e8f0 !important;
            border-radius: 16px !important;
            overflow: hidden !important;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 p-4 md:p-8">

    <div class="max-w-7xl mx-auto">

    {{-- ================= USER HEADER ================= --}}
@php
    use App\Models\Division;

    $authUser = auth()->user();

    // Get division IDs from user (assumes array or JSON)
    $divisionIds = is_array($authUser->divisions)
        ? $authUser->divisions
        : json_decode($authUser->divisions, true);

    // Fetch division names
    $divisionNames = Division::whereIn('id', $divisionIds ?? [])
        ->pluck('name')
        ->toArray();
@endphp

<div class="modern-card p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    
    {{-- USER INFO --}}
    <div>
        <h2 class="text-lg font-semibold text-slate-900">
            {{ $authUser->name }}
        </h2>
        <p class="text-sm text-slate-500">
            Divisions:
            <span class="font-medium text-slate-700">
                {{ !empty($divisionNames) ? implode(', ', $divisionNames) : 'No Division Assigned' }}
            </span>
        </p>
    </div>

    {{-- LOGOUT BUTTON --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button
            type="submit"
            class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition"
        >
            Logout
        </button>
    </form>

</div>

        {{-- ================= INSTRUCTIONS ================= --}}
        <div class="instructions mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-slate-700">
                <span class="font-semibold">Quick guide:</span> Scan QR code for Time In/Out · Use External Scanner or Camera · Records appear on the right
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ================= SCANNER ================= --}}
            <div class="modern-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-900">ESRRS Attendance</h1>
                        <p class="text-sm text-slate-500">Scan QR code to record attendance</p>
                    </div>
                </div>

                {{-- STATUS --}}
                @if(session('success'))
                <div class="status-success mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="status-error mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
                @endif

                {{-- HTTPS WARNING --}}
                <div id="httpsWarning" class="hidden mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-700 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Camera scanning requires <strong>HTTPS</strong> connection
                </div>

                {{-- MODE SWITCH --}}
                <div class="flex gap-2 mb-6">
                    <button id="scannerMode" class="mode-btn active flex-1 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        External Scanner
                    </button>
                    <button id="cameraMode" class="mode-btn flex-1 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Use Camera
                    </button>
                </div>

                {{-- FORM --}}
                <form action="{{ route('attendance.store') }}" method="POST" id="qrForm" class="mb-4">
                    @csrf
                    <input
                        type="text"
                        name="qr_code"
                        id="qr_code"
                        placeholder="Scan or type QR code..."
                        class="simple-input w-full"
                        autofocus
                    >
                    <button type="submit" class="hidden"></button>
                </form>

                {{-- CAMERA VIEW --}}
                <div id="cameraBox" class="hidden">
                    <div id="qr-reader" class="w-full"></div>
                    <p class="text-xs text-slate-500 mt-2 text-center">Position QR code within the frame</p>
                </div>

                <div class="flex justify-between text-xs text-slate-400 mt-4 pt-4 border-t border-slate-100">
                    <span>{{ now()->format('h:i A') }}</span>
                    <span>{{ now()->format('M d, Y') }}</span>
                </div>
            </div>

            {{-- ================= RECENT ATTENDANCE ================= --}}
<div class="modern-card p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Today's Attendance
        </h2>

        @php
        use App\Models\Attendance;
        use Carbon\Carbon;

        $authUser = auth()->user();

        // ================= USER DIVISIONS =================
        $divisionIds = is_array($authUser->divisions)
            ? $authUser->divisions
            : json_decode($authUser->divisions, true);

        $userDivisions = Division::whereIn('id', $divisionIds ?? [])
            ->pluck('name')
            ->toArray();

        // Normalize: remove "SDO - "
        $userDivisions = array_map(function ($d) {
            return strtoupper(trim(str_replace('SDO -', '', $d)));
        }, $userDivisions);

        // ================= COUNT (FILTERED) =================
        $count = Attendance::whereDate('created_at', Carbon::today())
            ->where(function ($q) use ($userDivisions) {

                $q->whereHas('athlete', function ($q2) use ($userDivisions) {
                    $q2->whereIn(\DB::raw('UPPER(school_division)'), $userDivisions);
                })

                ->orWhereHas('coach.school', function ($q2) use ($userDivisions) {
                    $q2->whereIn(\DB::raw('UPPER(division)'), $userDivisions);
                });

            })
            ->count();
        @endphp

        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-medium">
            {{ $count }} entries
        </span>
    </div>

    @php
    // ================= RECENT FILTERED =================
    $recent = Attendance::with(['athlete','coach.school'])
        ->whereDate('created_at', Carbon::today())
        ->where(function ($q) use ($userDivisions) {

            $q->whereHas('athlete', function ($q2) use ($userDivisions) {
                $q2->whereIn(\DB::raw('UPPER(school_division)'), $userDivisions);
            })

            ->orWhereHas('coach.school', function ($q2) use ($userDivisions) {
                $q2->whereIn(\DB::raw('UPPER(division)'), $userDivisions);
            });

        })
        ->latest()
        ->limit(15)
        ->get();
    @endphp

    <div class="overflow-y-auto max-h-[500px]">
        <table class="simple-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Division</th>
                    <th class="text-center">In</th>
                    <th class="text-center">Out</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $r)
                @php
                    $user = $r->athlete ?? $r->coach;
                    $name = $user->name ?? trim($user->first_name.' '.$user->last_name);
                    $division = $user->school_division ?? $user->school->division ?? '—';
                @endphp
                <tr>
                    <td class="font-medium">{{ $name }}</td>
                    <td class="text-slate-600">{{ $division }}</td>
                    <td class="text-center">
                        @if($r->time_in)
                            <span class="badge badge-green">
                                {{ Carbon::parse($r->time_in)->format('h:i A') }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($r->time_out)
                            <span class="badge badge-orange">
                                {{ Carbon::parse($r->time_out)->format('h:i A') }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-12 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        No attendance records for your division today
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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

        /* Mode button states */
        function setActiveMode(active) {
            [scannerBtn, cameraBtn].forEach(btn => btn.classList.remove('active'));
            if (active === 'scanner') {
                scannerBtn.classList.add('active');
            } else {
                cameraBtn.classList.add('active');
            }
        }

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

        let submitting = false;

function submitQR(){
    if(submitting) return;

    const value = qrInput.value.trim();
    if(value){
        submitting = true;
        qrForm.submit();
    }
}

qrInput.addEventListener('keydown', e=>{
    if(e.key === 'Enter'){
        e.preventDefault();
        submitQR();
    }
});

        /* Camera Scanner */
        cameraBtn.onclick = () => {
            if (!isSecure) return;
            setActiveMode('camera');
            cameraBox.classList.remove('hidden');
            qrInput.blur();
            startCamera();
        };

        scannerBtn.onclick = () => {
            setActiveMode('scanner');
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

        /* Initial active state */
        setActiveMode('scanner');
    </script>
</body>
</html>