<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Scanner - CEMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .scanner-screen {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .success-pulse {
            animation: pulse-green 2s infinite;
        }
        .error-pulse {
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">CEMS Attendance System</h1>
            <p class="text-white/80 text-lg">Scan your QR code to check in/out</p>
        </div>

        {{-- Main Scanner Interface --}}
        <div class="scanner-screen rounded-2xl shadow-2xl p-8 mb-8">
            {{-- Status Message --}}
            @if(session('success'))
                <div class="mb-8 p-6 bg-emerald-50 border border-emerald-200 rounded-xl success-pulse">
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-emerald-800">Attendance Recorded!</h3>
                            <p class="text-emerald-600 mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-6 bg-rose-50 border border-rose-200 rounded-xl error-pulse">
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-rose-800">Unable to Process</h3>
                            <p class="text-rose-600 mt-1">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Scanner Box --}}
            <div class="text-center">
                <div class="relative mb-6">
                    <div class="absolute -top-2 left-1/2 transform -translate-x-1/2">
                        <div class="bg-indigo-600 text-white px-4 py-1 rounded-full text-sm font-medium">
                            SCAN HERE
                        </div>
                    </div>
                    
                    <form action="{{ route('attendance.store') }}" method="POST" id="qrForm">
                        @csrf
                        <input
                            type="text"
                            name="qr_code"
                            id="qr_code"
                            placeholder="Waiting for QR scan..."
                            class="w-full p-6 text-2xl text-center border-4 border-dashed border-indigo-300 rounded-2xl bg-white/50 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all duration-300"
                            autofocus
                            autocomplete="off"
                        />
                        <button type="submit" class="hidden">Submit</button>
                    </form>
                </div>

                {{-- Instructions --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold">1</span>
                            </div>
                            <h3 class="font-semibold text-blue-800">First Scan</h3>
                        </div>
                        <p class="text-blue-600">Time In (Check-in)</p>
                    </div>
                    
                    <div class="p-4 bg-purple-50 rounded-xl">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold">2</span>
                            </div>
                            <h3 class="font-semibold text-purple-800">Second Scan</h3>
                        </div>
                        <p class="text-purple-600">Time Out (Check-out)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="scanner-screen rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Recent Check-ins</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="p-3 text-left">Time</th>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Type</th>
                            <th class="p-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            use App\Models\Attendance;
                            use Carbon\Carbon;
                            
                            $recent = Attendance::with(['athlete', 'coach'])
                                ->whereDate('created_at', Carbon::today())
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        
                       @foreach($recent as $record)
    @php
        $athlete = $record->athlete;
        $coach   = $record->coach;
        $userType = $athlete ? 'athlete' : 'coach';
        $user = $athlete ?? $coach;

        // Determine display name
        if ($athlete) {
            $fullName = trim($athlete->first_name 
                         . ' ' 
                         . ($athlete->middle_initial ? $athlete->middle_initial . '. ' : '') 
                         . $athlete->last_name);
        } else {
            $fullName = $coach->name;
        }

        $isTimeIn = is_null($record->time_out);
        $time = $isTimeIn ? $record->time_in : $record->time_out;
    @endphp

    <tr class="border-b hover:bg-slate-50">
        <td class="p-3 font-medium">
            {{ Carbon::parse($time)->format('h:i A') }}
        </td>
        <td class="p-3">
            <div class="flex items-center gap-2">
                @if($user && $user->profile_photo)
                    <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                         class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center">
                        <span class="text-slate-500 text-xs font-medium">
                            {{ substr($fullName ?? '?', 0, 1) }}
                        </span>
                    </div>
                @endif
                <span>{{ $fullName ?? 'Unknown' }}</span>
            </div>
        </td>
        <td class="p-3">
            <span class="px-2 py-1 rounded-full text-xs font-medium 
                {{ $athlete ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                {{ $athlete ? 'Athlete' : 'Coach' }}
            </span>
        </td>
        <td class="p-3">
            <span class="px-2 py-1 rounded-full text-xs font-medium 
                {{ $isTimeIn ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $isTimeIn ? 'Checked In' : 'Checked Out' }}
            </span>
        </td>
    </tr>
@endforeach

                        
                        @if($recent->isEmpty())
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    No attendance records yet today
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-6">
                <a href="{{ route('welcome') }}" 
                   class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        const qrInput = document.getElementById('qr_code');
        const qrForm = document.getElementById('qrForm');

        // Auto-submit on scan (most QR scanners input data and trigger 'change')
        qrInput.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                // Add a small delay to ensure QR code is fully read
                setTimeout(() => {
                    qrForm.submit();
                }, 100);
            }
        });

        // Also handle change event
        qrInput.addEventListener('change', function() {
            if (this.value.trim().length > 0) {
                qrForm.submit();
            }
        });

        // Auto-clear and focus after message display
        @if(session('success') || session('error'))
            setTimeout(function() {
                qrInput.value = '';
                qrInput.focus();
                
                // Auto-hide success/error messages after 3 seconds
                const messages = document.querySelectorAll('[class*="pulse"]');
                messages.forEach(msg => {
                    setTimeout(() => {
                        msg.style.opacity = '0';
                        msg.style.transition = 'opacity 0.5s';
                        setTimeout(() => msg.remove(), 500);
                    }, 3000);
                });
            }, 100);
        @endif

        // Beep sound for feedback (optional)
        function playBeep(type) {
            if (typeof Audio !== 'undefined') {
                const audio = new Audio();
                audio.src = type === 'success' 
                    ? 'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA==' // Success beep
                    : 'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAZGF0YQQAAAAAAA=='; // Error beep
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        }

        // Initial focus
        qrInput.focus();
        
        // Keep focus on input
        setInterval(() => {
            if (document.activeElement !== qrInput) {
                qrInput.focus();
            }
        }, 1000);
    </script>
</body>
</html>