<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Scanner - CEMS</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

/* Glass Card */
.card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(8px);
}

/* Pulsing notifications */
.success-pulse {
    animation: pulse-green 2s infinite;
}
.error-pulse {
    animation: pulse-red 2s infinite;
}
@keyframes pulse-green {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
    70% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}
@keyframes pulse-red {
    0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
    70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
    100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}
</style>
</head>
<body class="relative flex items-center justify-center p-4 bg-gray-100">

<div class="relative w-full max-w-7xl flex flex-col lg:flex-row gap-6 z-10">

    {{-- Scanner Card --}}
    <div class="flex-1 card rounded-xl p-8 flex flex-col justify-between shadow-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">CEMS Attendance</h1>
            <p class="text-gray-600 mt-1">Scan your QR code to check in/out</p>
        </div>

        {{-- Status Messages --}}
        @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg success-pulse flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm text-emerald-800">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg error-pulse flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm text-rose-800">{{ session('error') }}</span>
        </div>
        @endif

        {{-- QR Scanner --}}
        <form action="{{ route('attendance.store') }}" method="POST" id="qrForm" class="flex flex-col items-center w-full">
            @csrf
            <input type="text" name="qr_code" id="qr_code"
                placeholder="Waiting for QR scan..."
                class="w-full p-5 text-center text-xl border-2 border-dashed border-indigo-300 rounded-lg bg-white/50 focus:outline-none focus:border-indigo-500 transition"
                autofocus autocomplete="off">
            <button type="submit" class="hidden">Submit</button>
        </form>

        {{-- Instructions --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="p-3 bg-blue-50 rounded-lg flex items-center gap-3">
                <div class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                <div>
                    <p class="text-sm font-semibold text-black">First Scan</p>
                    <p class="text-xs text-black">Check-in</p>
                </div>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg flex items-center gap-3">
                <div class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                <div>
                    <p class="text-sm font-semibold text-black">Second Scan</p>
                    <p class="text-xs text-black">Check-out</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity Card --}}
    <div class="flex-1 card rounded-xl p-6 overflow-y-auto max-h-[80vh] shadow-md">
        <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Recent Check-ins</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Time</th>
                        <th class="p-2 text-left">Name</th>
                        <th class="p-2 text-left">Type</th>
                        <th class="p-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        use App\Models\Attendance;
                        use Carbon\Carbon;
                        $recent = Attendance::with(['athlete','coach'])->whereDate('created_at', Carbon::today())->orderBy('created_at','desc')->limit(10)->get();
                    @endphp
                    @foreach($recent as $record)
                    @php
                        $athlete = $record->athlete;
                        $coach = $record->coach;
                        $user = $athlete ?? $coach;
                        $fullName = $athlete ? trim($athlete->first_name . ' ' . ($athlete->middle_initial ? $athlete->middle_initial.'. ' : '') . $athlete->last_name) : $coach->name;
                        $isTimeIn = is_null($record->time_out);
                        $time = $isTimeIn ? $record->time_in : $record->time_out;
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 font-medium">{{ Carbon::parse($time)->format('h:i A') }}</td>
                        <td class="p-2 flex items-center gap-2">
                            @if($user && $user->profile_photo)
                            <img src="{{ asset('storage/'.$user->profile_photo) }}" class="w-6 h-6 rounded-full object-cover">
                            @else
                            <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs text-gray-600">
                                {{ substr($fullName ?? '?', 0, 1) }}
                            </div>
                            @endif
                            {{ $fullName ?? 'Unknown' }}
                        </td>
                        <td class="p-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $athlete ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $athlete ? 'Athlete' : 'Coach' }}
                            </span>
                        </td>
                        <td class="p-2">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $isTimeIn ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $isTimeIn ? 'Checked In' : 'Checked Out' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @if($recent->isEmpty())
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-400">No attendance records today</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Back to Home (Bottom Center) --}}
<div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-20">
    <a href="{{ route('viewer.home') }}" 
       class="bg-white/90 hover:bg-white text-black hover:text-black font-medium px-6 py-3 rounded-full shadow flex items-center gap-2 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back
    </a>
</div>

<script>
const qrInput = document.getElementById('qr_code');
const qrForm = document.getElementById('qrForm');

qrInput.addEventListener('input', function(){ if(this.value.trim().length>0) setTimeout(()=>qrForm.submit(),100)});
qrInput.addEventListener('change', function(){ if(this.value.trim().length>0) qrForm.submit(); });

@if(session('success') || session('error'))
setTimeout(()=>{ qrInput.value=''; qrInput.focus(); const messages=document.querySelectorAll('[class*="pulse"]'); messages.forEach(msg=>setTimeout(()=>msg.style.opacity='0',3000));},100);
@endif

qrInput.focus();
setInterval(()=>{if(document.activeElement!==qrInput) qrInput.focus();},1000);
</script>

</body>
</html>
