<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAVRAA 2026 | Athlete Profile</title>
    <!-- Tailwind CSS + Font Awesome + Google Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* CAVRAA 2026 inspired multi-color gradient header */
        .cavraa-header {
            background: linear-gradient(135deg, #E31B23 0%, #2C5F9E 35%, #FDB913 70%, #2A9D6E 100%);
            background-size: 150% 150%;
        }
        
        /* Sporty accent ring animation (multi-color pulse) */
        @keyframes cavraaGlow {
            0% { box-shadow: 0 0 0 0 rgba(227, 27, 35, 0.4); }
            33% { box-shadow: 0 0 0 8px rgba(45, 95, 158, 0.3); }
            66% { box-shadow: 0 0 0 8px rgba(253, 185, 19, 0.3); }
            100% { box-shadow: 0 0 0 0 rgba(42, 157, 110, 0.4); }
        }
        .profile-ring {
            animation: cavraaGlow 3s infinite;
        }
        
        /* Stat cards hover effect */
        .stat-card-hover {
            transition: all 0.25s ease;
        }
        .stat-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 22px -10px rgba(0, 0, 0, 0.2);
        }
        
        /* Custom background pattern - sporty confetti dots */
        .bg-sport-pattern {
            background-image: radial-gradient(circle at 15% 30%, rgba(255,255,245,0.25) 1.8px, transparent 1.8px);
            background-size: 22px 22px;
        }
        
        /* Medal card specific colors */
        .medal-gold-bg { background: linear-gradient(135deg, #FFF8E7, #FFE5B4); border-color: #FDB913; }
        .medal-silver-bg { background: linear-gradient(135deg, #F5F7FA, #E8EDF2); border-color: #B0BEC5; }
        .medal-bronze-bg { background: linear-gradient(135deg, #FDF4E3, #FCE4C1); border-color: #CD7F32; }
        
        /* Custom scrollbar for modal */
        #modalContent::-webkit-scrollbar { width: 4px; }
        #modalContent::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        #modalContent::-webkit-scrollbar-thumb { background: #E31B23; border-radius: 10px; }
        
        /* vibrant badge style */
        .vibrant-badge {
            background: linear-gradient(110deg, #E31B23, #FDB913);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-red-50 via-blue-50 to-yellow-50 min-h-screen flex items-center justify-center p-4 md:p-6">

    <!-- MAIN CARD: CAVRAA 2026 SPORT EDITION -->
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden transition-all duration-300 border border-white/40 relative">
        
        <!-- multi-color ribbon accent top corner -->
        <div class="absolute top-0 right-0 w-20 h-20 overflow-hidden z-20">
            <div class="absolute transform rotate-45 bg-gradient-to-r from-[#E31B23] via-[#2C5F9E] to-[#FDB913] w-28 h-6 top-5 right-[-20px] shadow-md"></div>
        </div>
        
        {{-- HEADER: CAVRAA 2026 SPIRIT --}}
        <div class="relative cavraa-header px-6 pt-6 pb-10 text-white">
            <div class="absolute inset-0 opacity-20 bg-sport-pattern"></div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md rounded-full px-3 py-1 text-xs font-extrabold tracking-wider border border-white/30">
                        <i class="fas fa-flag-checkered text-yellow-300 text-xs"></i> CAVRAA 2026
                    </span>
                    <h1 class="text-2xl font-black mt-3 tracking-tight flex items-center gap-2">
                        <i class="fas fa-medal text-yellow-300 text-2xl drop-shadow-sm"></i> 
                        SDO - {{ $athlete->school_division }}
                    </h1>
                </div>
                <div class="bg-white/30 rounded-full w-10 h-10 flex items-center justify-center backdrop-blur-sm shadow-md">
                    <i class="fas fa-running text-white text-lg"></i>
                </div>
            </div>
            <div class="relative z-10 mt-2 flex items-center gap-2">
                <div class="h-1.5 w-8 bg-gradient-to-r from-red-400 via-yellow-400 to-green-400 rounded-full"></div>
                <p class="text-white/90 text-sm font-semibold tracking-wide">eSRRS</p>
            </div>
            <!-- angled white bottom cut -->
            <div class="absolute bottom-0 left-0 right-0 h-6 bg-white rounded-t-3xl"></div>
        </div>

        {{-- PROFILE SECTION --}}
        <div class="px-6 pb-2 relative">
            <!-- Profile image with multi-color ring -->
            <div class="flex justify-center -mt-12 mb-4 relative">
                <div class="relative">
                    <img 
    src="{{ $athlete->profile_photo 
        ? asset('storage/' . $athlete->profile_photo) 
        : asset('storage/avatar.jpg') }}"
    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-xl profile-ring bg-white"
    alt="Athlete photo">
                </div>
            </div>

            {{-- NAME & EVENT with colorful tags --}}
            <div class="text-center">
                <h2 class="text-2xl font-black text-gray-800 tracking-tight flex justify-center items-center gap-2 flex-wrap">
                    {{ $athlete->last_name }}, {{ $athlete->first_name }}
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-gradient-to-r from-[#E31B23] to-[#FDB913] rounded-full shadow-sm">
                        <i class="fas fa-check-circle text-white text-xs"></i>
                    </span>
                </h2>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <p class="text-md font-bold text-[#2A9D6E] bg-green-50 px-3 py-0.5 rounded-full border border-green-200">
                        {{ $athlete->event ?? 'Track & Field • Sprint' }}
                    </p>
                </div>
                
                {{-- STATUS BADGE dynamic --}}
                <div class="mt-3">
                    @php
                        $statusColor = $athlete->status === 'Active' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200';
                        $statusIcon = $athlete->status === 'Active' ? 'fa-bolt' : 'fa-pause-circle';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-bold rounded-full border {{ $statusColor }} shadow-sm">
                        <i class="fas {{ $statusIcon }} text-xs"></i>
                        <span>{{ $athlete->status }}</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- 🏅 MEDAL STATS (CAVRAA colors: Red, Blue, Green, Yellow, Pink accent) --}}
        <div class="grid grid-cols-3 gap-3 px-6 mt-6">
            <!-- GOLD (Yellow/Orange energy) -->
            <div onclick="openModal('Gold')" 
                 class="cursor-pointer bg-gradient-to-br from-yellow-100 to-amber-50 rounded-2xl p-3 text-center shadow-sm border-l-4 border-[#FDB913] stat-card-hover">
                <i class="fas fa-crown text-yellow-600 text-xl"></i>
                <p class="text-xs text-gray-600 font-bold mt-1">GOLD</p>
                <p class="text-xl font-black text-gray-800">{{ $goldCount ?? 0 }}</p>
            </div>
            <!-- SILVER (Cool blue-silver) -->
            <div onclick="openModal('Silver')" 
                 class="cursor-pointer bg-gradient-to-br from-slate-100 to-gray-100 rounded-2xl p-3 text-center shadow-sm border-l-4 border-[#2C5F9E] stat-card-hover">
                <i class="fas fa-medal text-slate-500 text-xl"></i>
                <p class="text-xs text-gray-600 font-bold mt-1">SILVER</p>
                <p class="text-xl font-black text-gray-800">{{ $silverCount ?? 0 }}</p>
            </div>
            <!-- BRONZE (Warm terracotta) -->
            <div onclick="openModal('Bronze')" 
                 class="cursor-pointer bg-gradient-to-br from-orange-100 to-amber-100 rounded-2xl p-3 text-center shadow-sm border-l-4 border-[#CD7F32] stat-card-hover">
                <i class="fas fa-medal text-amber-700 text-xl"></i>
                <p class="text-xs text-gray-600 font-bold mt-1">BRONZE</p>
                <p class="text-xl font-black text-gray-800">{{ $bronzeCount ?? 0 }}</p>
            </div>
        </div>
    

        {{-- DETAILS CARD (sporty vibrant info grid) --}}
        <div class="mx-6 mt-5 mb-2 bg-white rounded-2xl border border-red-100 shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 via-blue-50 to-yellow-50 px-5 py-2.5 border-b border-red-100 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-[#E31B23] text-sm"></i>
                <h3 class="font-black text-gray-700 text-sm uppercase tracking-wide">ATHLETE CREDENTIALS</h3>
                <div class="ml-auto flex gap-1">
                    <span class="w-2 h-2 rounded-full bg-[#E31B23]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#2C5F9E]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#FDB913]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#2A9D6E]"></span>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                <!-- Division / Level -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-red-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-layer-group text-[#2C5F9E] w-4"></i> Division</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->division }}</span>
                </div>
                <!-- Category -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-yellow-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-tag text-[#FDB913] w-4"></i> Category</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->category }}</span>
                </div>
                <!-- Sex -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-pink-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-venus-mars text-[#E31B23] w-4"></i> Sex</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->sex }}</span>
                </div>
                <!-- School -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-green-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-school text-[#2A9D6E] w-4"></i> School</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->school->name ?? 'N/A' }}</span>
                </div>
                <!-- Division (SDO) -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-blue-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-building text-[#2C5F9E] w-4"></i> Division SDO</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->school_division }}</span>
                </div>
                <!-- Municipality -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-amber-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-map-marker-alt text-[#FDB913] w-4"></i> Municipality</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->municipality }}</span>
                </div>
                <!-- Legislative District -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-red-50/40 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-landmark text-[#E31B23] w-4"></i> Legislative</span>
                    <span class="font-semibold text-gray-800">{{ $athlete->legislative_district ?? 'N/A' }}</span>
                </div>
                <!-- LRN -->
                <div class="flex justify-between items-center px-5 py-3 hover:bg-gray-50 transition">
                    <span class="text-gray-500 text-sm flex items-center gap-2"><i class="fas fa-id-card text-indigo-500 w-4"></i> LRN</span>
                    <span class="font-mono text-sm font-semibold text-gray-800">{{ $athlete->lrn }}</span>
                </div>
            </div>
        </div>

        {{-- FOOTER with CAVRAA BRANDING and 2026 pride --}}
        <div class="mt-6 mb-4 mx-6">
            <div class="bg-gradient-to-r from-[#E31B23] via-[#2C5F9E] to-[#FDB913] rounded-xl p-0.5 shadow-lg">
                <div class="bg-white rounded-xl p-2.5 flex justify-between items-center">
                    <div class="flex items-center gap-2 pl-1">
                        <i class="fas fa-shield-alt text-[#E31B23] text-sm"></i>
                        <span class="text-xs font-black text-gray-800 tracking-wide">CAVRAA 2026 • OFFICIAL VERIFIED</span>
                    </div>
                    <div class="flex gap-2">
                        <i class="fab fa-strava text-[#2C5F9E] text-sm"></i>
                        <i class="fas fa-medal text-[#FDB913] text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="text-center text-[11px] text-gray-500 font-semibold mt-3 flex justify-center gap-5">
                <span class="flex items-center gap-1"><i class="far fa-calendar-alt text-pink-500"></i> Season 2026 • CAVRAA</span>
                <span class="flex items-center gap-1"><i class="fas fa-running text-green-600"></i> KAISAng Lakas ng Isports sa Dos</span>
                <span class="flex items-center gap-1"><i class="fas fa-flag-checkered text-red-500"></i> Santiago City</span>
            </div>
             {{-- ================= SCAN ANOTHER ATHLETE BUTTON ================= --}}
<div class="w-full max-w-lg mt-4 flex flex-col gap-3">

    <button 
        onclick="openInlineScanner()"
        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg flex items-center justify-center gap-2 transition-all duration-200">
        
        <i class="fas fa-camera"></i>
        Scan Another Athlete
    </button>

    <a href="{{ route('viewer.home') }}"
       class="w-full text-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 rounded-xl shadow-sm flex items-center justify-center gap-2 transition">
        <i class="fas fa-home"></i>
        Back to Home
    </a>

</div>
        </div>
    </div>
    
    

    {{-- MEDAL MODAL (with vibrant CAVRAA styling) --}}
    <div id="medalModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-5 relative border-t-8 border-[#FDB913]">
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 bg-gray-100 rounded-full w-7 h-7 flex items-center justify-center">
                <i class="fas fa-times text-sm"></i>
            </button>
            <h3 id="modalTitle" class="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-medal text-[#E31B23]"></i> Medal Details
            </h3>
            <div id="modalContent" class="space-y-3 max-h-80 overflow-y-auto pr-1 text-sm">
                <!-- dynamic medal entries -->
                <p class="text-gray-400 text-center py-4">Loading achievements...</p>
            </div>
            <div class="mt-4 text-right">
                <button onclick="closeModal()" class="bg-gradient-to-r from-[#2C5F9E] to-[#2A9D6E] text-white px-5 py-2 rounded-full text-xs font-bold shadow-md">Close</button>
            </div>
        </div>
    </div>

    {{-- ================= INLINE QR SCANNER (UPGRADED UI) ================= --}}
<div id="inlineScannerModal"
     class="fixed inset-0 z-[120] bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-green-600 to-green-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-camera text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg">Scan Athlete ID</h2>
                    <p class="text-white/80 text-sm">Scan to view athlete details</p>
                </div>
            </div>

            <button onclick="closeInlineScanner()" class="text-white/90 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- BODY --}}
        <div class="p-5">

            {{-- CAMERA BOX --}}
            <div class="bg-gray-100 rounded-xl p-3 border border-gray-200">
                <div id="inline-qr-reader" class="w-full overflow-hidden rounded-lg"></div>
            </div>

            {{-- DESCRIPTION --}}
            <p class="mt-4 text-sm text-gray-600">
                Point your camera at the athlete QR code. Works for both old and new IDs.
            </p>

            {{-- ERROR --}}
            <div id="scannerErrorBox"
                 class="mt-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm hidden">
            </div>

            {{-- LOADING --}}
            <div id="scannerLoadingBox"
                 class="mt-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm hidden">
                Opening camera...
            </div>

            {{-- ACTIONS --}}
            <div class="mt-5 flex items-center justify-end gap-3">
                <button onclick="closeInlineScanner()"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

    <script>
        // Medal data from backend (passed via Blade)
        const medals = @json($medals ?? []);
        
        function openModal(type) {
            const modal = document.getElementById('medalModal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('modalContent');
            
            let medalColor = '';
            if (type === 'Gold') medalColor = 'text-[#FDB913]';
            else if (type === 'Silver') medalColor = 'text-[#2C5F9E]';
            else medalColor = 'text-[#CD7F32]';
            
            title.innerHTML = `<i class="fas fa-medal ${medalColor}"></i> ${type} Medal Achievements`;
            
            let data = medals[type] || [];
            
            if (data.length === 0) {
                content.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <i class="fas fa-trophy text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500 font-medium">No ${type} medals recorded yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Stay tuned for CAVRAA 2026 results!</p>
                    </div>
                `;
            } else {
                content.innerHTML = data.map(item => `
                    <div class="p-3 rounded-xl bg-gradient-to-r from-gray-50 to-white border-l-4 border-[${type === 'Gold' ? '#FDB913' : (type === 'Silver' ? '#2C5F9E' : '#CD7F32')}] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-extrabold text-gray-800 text-sm flex items-center gap-1">
                                    <i class="fas fa-🏟️ text-[#E31B23] text-xs"></i> ${item.event?.name ?? 'CAVRAA Event'}
                                </p>
                                <p class="text-gray-600 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-futbol text-[#2A9D6E] text-xs"></i> Sport: ${item.sport?.name ?? 'Athletics'}
                                </p>
                                <p class="text-gray-500 text-[11px] mt-1 flex items-center gap-1">
                                    <i class="fas fa-tag"></i> Category: ${item.type?.type_name ?? 'Open Division'}
                                </p>
                            </div>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full ${type === 'Gold' ? 'bg-yellow-100 text-yellow-700' : (type === 'Silver' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700')}">${type}</span>
                        </div>
                    </div>
                `).join('');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeModal() {
            const modal = document.getElementById('medalModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // small interactive pulse on profile image to mimic sport energy
        const profileImg = document.querySelector('.profile-ring');
        if(profileImg) {
            setInterval(() => {
                profileImg.style.transform = 'scale(1.01)';
                setTimeout(() => { profileImg.style.transform = 'scale(1)'; }, 180);
            }, 2800);
        }
        
        // optional: close modal on outside click
        window.onclick = function(e) {
            const modal = document.getElementById('medalModal');
            if (e.target === modal) closeModal();
        }
    </script>

    <script>
        let inlineScanner = null;

function showLoading(show = true) {
    document.getElementById('scannerLoadingBox').classList.toggle('hidden', !show);
}

function showError(message = '') {
    const box = document.getElementById('scannerErrorBox');
    if (!message) {
        box.classList.add('hidden');
        box.innerText = '';
        return;
    }
    box.innerText = message;
    box.classList.remove('hidden');
}

function openInlineScanner() {
    const modal = document.getElementById('inlineScannerModal');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    showError('');
    showLoading(true);

    startInlineScanner();
}

async function closeInlineScanner() {
    const modal = document.getElementById('inlineScannerModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    showLoading(false);
    showError('');

    if (inlineScanner) {
        try {
            await inlineScanner.stop();
            await inlineScanner.clear();
        } catch (e) {}
        inlineScanner = null;
    }
}

function normalizeQR(text) {
    if (!text) return null;

    text = text.replace(/[\s\r\n\t]+/g, '');

    const match = text.match(/(eSRRS-[A-Z0-9-]+)/i);
    return match ? match[1].toUpperCase() : null;
}

async function startInlineScanner() {
    try {
        inlineScanner = new Html5Qrcode("inline-qr-reader");

        await inlineScanner.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            async (decodedText) => {

                const clean = normalizeQR(decodedText);

                if (!clean) {
                    showError("Invalid QR code. Try again.");
                    return;
                }

                await inlineScanner.stop();

                window.location.href = "/scan/" + encodeURIComponent(clean);
            }
        );

        showLoading(false);

    } catch (e) {
        showLoading(false);
        showError("Camera access failed. Please allow permission.");
    }
}
    </script>
</body>
</html>