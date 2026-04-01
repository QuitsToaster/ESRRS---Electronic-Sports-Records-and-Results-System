<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Scanner · ESRRS</title>
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
        }
        
        /* Modern card style */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(0px);
            border-radius: 28px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(203, 213, 225, 0.5);
        }
        
        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.08);
        }
        
        /* Enhanced input */
        .attendance-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            padding: 1rem 1.25rem;
            transition: all 0.2s ease;
            background: #ffffff;
            font-size: 1rem;
        }
        
        .attendance-input:focus {
            border-color: #3b82f6;
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            transform: scale(1.01);
        }
        
        /* Mode buttons */
        .mode-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            background: white;
            border: 1.5px solid #e2e8f0;
            color: #475569;
            cursor: pointer;
        }
        
        .mode-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        
        .mode-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        
        /* Status toasts */
        .toast-success {
            background: #10b981;
            color: white;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            animation: slideIn 0.3s ease-out;
        }
        
        .toast-error {
            background: #ef4444;
            color: white;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Stats cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.08);
        }
        
        /* Attendance list items */
        .attendance-item {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
            border: 1px solid #f1f5f9;
        }
        
        .attendance-item:hover {
            transform: translateX(4px);
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
        }
        
        /* Scanner box */
        #qr-reader {
            border: 2px solid #3b82f6 !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            background: #ffffff !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Spinner */
        .spinner {
            border: 2px solid #e2e8f0;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Pulse animation for camera indicator */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .pulse-dot {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
</head>

<body class="min-h-screen p-4 md:p-8">

    <div class="max-w-7xl mx-auto">

    {{-- ================= USER HEADER ================= --}}
@php
    use App\Models\Division;
    use App\Models\Attendance;
    use Carbon\Carbon;

    $authUser = auth()->user();

    // Get division IDs from user (assumes array or JSON)
    $divisionIds = is_array($authUser->divisions)
        ? $authUser->divisions
        : json_decode($authUser->divisions, true);

    // Fetch division names
    $divisionNames = Division::whereIn('id', $divisionIds ?? [])
        ->pluck('name')
        ->toArray();

    // Normalize divisions for filtering
    $userDivisions = array_map(function ($d) {
        return strtoupper(trim(str_replace('SDO -', '', $d)));
    }, $divisionNames);

    // Today's stats
    $todayStats = Attendance::whereDate('created_at', Carbon::today())
        ->where(function ($q) use ($userDivisions) {
            $q->whereHas('athlete', function ($q2) use ($userDivisions) {
                $q2->whereIn(\DB::raw('UPPER(school_division)'), $userDivisions);
            })->orWhereHas('coach.school', function ($q2) use ($userDivisions) {
                $q2->whereIn(\DB::raw('UPPER(division)'), $userDivisions);
            });
        })
        ->get();

    $totalScans = $todayStats->count();
    $checkedIn = $todayStats->whereNotNull('time_in')->count();
    $checkedOut = $todayStats->whereNotNull('time_out')->count();
    $stillInside = $checkedIn - $checkedOut;
@endphp

<div class="modern-card p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        {{-- USER INFO --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    {{ $authUser->name }}
                </h2>
                <p class="text-sm text-slate-500">
                    Managing: 
                    <span class="font-medium text-slate-600">
                        {{ !empty($divisionNames) ? implode(', ', $divisionNames) : 'No Division Assigned' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- LIVE TIME --}}
        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-2xl font-bold text-slate-700" id="liveTime">--:--:--</div>
                <div class="text-xs text-slate-400" id="liveDate">{{ now()->format('l, F j, Y') }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition shadow-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <div class="stat-card text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $totalScans }}</div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mt-1">Total Scans</div>
        </div>
        <div class="stat-card text-center">
            <div class="text-2xl font-bold text-emerald-600">{{ $checkedIn }}</div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mt-1">Timed In</div>
        </div>
        <div class="stat-card text-center">
            <div class="text-2xl font-bold text-amber-600">{{ $checkedOut }}</div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mt-1">Timed Out</div>
        </div>
        <div class="stat-card text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ $stillInside }}</div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mt-1">Inside Quarters</div>
        </div>
    </div>
</div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ================= SCANNER ================= --}}
            <div class="modern-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">QR Scanner</h1>
                        <p class="text-sm text-slate-500">Scan to record time in/out</p>
                    </div>
                </div>

                {{-- STATUS TOASTS --}}
                @if(session('success'))
                <div class="toast-success mb-4 flex items-center gap-3" id="successToast">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="flex-1">{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="opacity-70 hover:opacity-100">✕</button>
                </div>
                @endif

                @if(session('error'))
                <div class="toast-error mb-4 flex items-center gap-3" id="errorToast">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="flex-1">{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="opacity-70 hover:opacity-100">✕</button>
                </div>
                @endif

                {{-- HTTPS WARNING --}}
                <div id="httpsWarning" class="hidden mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Camera scanning requires <strong>HTTPS</strong> connection</span>
                </div>

                {{-- MODE SWITCH --}}
                <div class="flex gap-3 mb-6">
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
                        System Camera
                    </button>
                </div>

                {{-- FORM --}}
                <form action="{{ route('attendance.store') }}" method="POST" id="qrForm" class="mb-4">
                    @csrf
                    <div class="relative">
                        <input type="text" name="qr_code" id="qr_code" placeholder="Enter or scan QR code..." class="attendance-input w-full" autofocus>
                        <div id="scanningLoader" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                            <div class="spinner"></div>
                        </div>
                    </div>
                    <button type="submit" class="hidden"></button>
                </form>

                {{-- CAMERA VIEW --}}
                <div id="cameraBox" class="hidden">
                    <div id="qr-reader" class="w-full"></div>
                    <div class="flex items-center justify-center gap-2 mt-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full pulse-dot"></div>
                        <p class="text-xs text-slate-500">Camera ready — position QR code within frame</p>
                    </div>
                </div>

                <div class="flex justify-between text-xs text-slate-400 mt-4 pt-4 border-t border-slate-100">
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="currentTime">{{ now()->format('h:i A') }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ now()->format('M d, Y') }}
                    </span>
                </div>
            </div>

            {{-- ================= RECENT ATTENDANCE ================= --}}
<div class="modern-card p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Live Attendance Feed
        </h2>
        <button onclick="location.reload()" class="text-slate-400 hover:text-slate-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>

    @php
    // ================= RECENT FILTERED =================
    $recent = Attendance::with(['athlete', 'coach.school'])
        ->whereDate('created_at', Carbon::today())
        ->where(function ($q) use ($userDivisions) {
            $q->whereHas('athlete', function ($q2) use ($userDivisions) {
                $q2->whereIn(\DB::raw('UPPER(school_division)'), $userDivisions);
            })->orWhereHas('coach.school', function ($q2) use ($userDivisions) {
                $q2->whereIn(\DB::raw('UPPER(division)'), $userDivisions);
            });
        })
        ->latest()
        ->limit(20)
        ->get();
    @endphp

    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
        @forelse($recent as $r)
        @php
            $user = $r->athlete ?? $r->coach;
            $name = $user->name ?? trim($user->first_name.' '.$user->last_name);
            $division = $user->school_division ?? $user->school->division ?? '—';
            $avatarLetter = strtoupper(substr($name, 0, 1));
        @endphp
        <div class="attendance-item flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-semibold">{{ $avatarLetter }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-700 truncate">{{ $name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $division }} - {{ $r->athlete->event ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($r->time_in)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ Carbon::parse($r->time_in)->format('h:i A') }}
                    </span>
                @endif
                @if($r->time_out)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        {{ Carbon::parse($r->time_out)->format('h:i A') }}
                    </span>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-slate-400 text-sm">No attendance records yet today</p>
            <p class="text-xs text-slate-300 mt-1">Scan a QR code to get started</p>
        </div>
        @endforelse
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
        const scanningLoader = document.getElementById('scanningLoader');

        let qrScanner;

        // Live time update
        function updateLiveTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const timeElem = document.getElementById('liveTime');
            if (timeElem) timeElem.textContent = timeStr;
            
            const currentTimeElem = document.getElementById('currentTime');
            if (currentTimeElem) currentTimeElem.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        updateLiveTime();
        setInterval(updateLiveTime, 1000);

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
        const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';

        /* Disable camera if not secure */
        if (!isSecure) {
            cameraBtn.disabled = true;
            cameraBtn.classList.add('opacity-50', 'cursor-not-allowed');
            httpsWarning.classList.remove('hidden');
        }

        /* External Scanner */
        let submitting = false;

        function submitQR() {
            if(submitting) return;
            const value = qrInput.value.trim();
            if(value){
                submitting = true;
                if(scanningLoader) scanningLoader.classList.remove('hidden');
                qrForm.submit();
            }
        }

        qrInput.addEventListener('keydown', e => {
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
                    if(scanningLoader) scanningLoader.classList.remove('hidden');
                    qrForm.submit();
                },
                () => {}
            ).catch(() => alert("Camera access failed. Please allow permission or use HTTPS."));
        }

        function stopCamera() {
            if (qrScanner) qrScanner.stop().then(() => qrScanner.clear());
            qrScanner = null;
        }

        /* Auto-hide toasts */
        setTimeout(() => {
            const successToast = document.getElementById('successToast');
            const errorToast = document.getElementById('errorToast');
            if(successToast) setTimeout(() => successToast.remove(), 4000);
            if(errorToast) setTimeout(() => errorToast.remove(), 4000);
        }, 100);

        /* Initial active state */
        setActiveMode('scanner');
    </script>
</body>
</html>