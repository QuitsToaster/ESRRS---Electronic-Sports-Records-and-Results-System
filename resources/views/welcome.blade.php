<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CAVRAA Monitoring System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    {{-- ================= DATA PRIVACY MODAL ================= --}}
    <div id="privacyModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm">

        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 mx-4 animate-fade-in">

            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                Data Privacy Notice
            </h2>

            <div class="text-gray-700 text-sm leading-relaxed mb-6 max-h-60 overflow-y-auto">
                <p class="mb-3">
                    In compliance with the Data Privacy Act of 2012 (RA 10173), the CAVRAA Monitoring System
                    collects, processes, and stores personal information strictly for event monitoring,
                    attendance tracking, and system management purposes.
                </p>

                <p class="mb-3">
                    By accessing this system, you acknowledge that your data may be collected and used
                    in accordance with our privacy policies, security measures, and legal obligations.
                </p>

                <p>
                    If you do not agree with these terms, you must discontinue using this system.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button onclick="disagreePrivacy()"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white font-semibold transition">
                    Disagree
                </button>

                <button onclick="agreePrivacy()"
                        class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-semibold transition">
                    Agree
                </button>
            </div>
        </div>
    </div>
    {{-- ================= END PRIVACY MODAL ================= --}}

    {{-- ================= MAIN CONTENT ================= --}}
@php
use App\Models\LayoutSetting;
$welcomeBg = LayoutSetting::where('key', 'welcome_image')->first();
@endphp

<div class="relative min-h-screen flex flex-col justify-center items-center px-4"
     style="background-image: url('{{ $welcomeBg ? asset('storage/' . $welcomeBg->value) : asset('images/cavraa.jpg') }}'); 
            background-size: cover; background-position: center;">


        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

        {{-- Content --}}
        <div class="relative max-w-3xl w-full text-center z-10">

            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">
                {{ config('app.name', 'CAVRAA Monitoring System') }}
            </h1>

            <p class="text-gray-200 mb-8 text-lg md:text-xl animate-fade-in delay-200">
                Monitor, manage, and track your school sports events in real-time.
            </p>

            <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-4 animate-fade-in delay-400">
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Admin Login
                </a>

                <a href="{{ route('viewer.home') }}"
                   class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Viewer Access
                </a>

                <a href="{{ route('attendance.check') }}"
                   class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Check Attendance
                </a>
            </div>
        </div>

        <footer class="mt-12 text-gray-300 text-sm text-center z-10 relative animate-fade-in delay-800">
            © {{ date('Y') }} CAVRAA Monitoring System - SDO Santiago City
        </footer>
    </div>

    {{-- ================= JAVASCRIPT ================= --}}
    <script>
        function agreePrivacy() {
            document.getElementById('privacyModal').classList.add('hidden');
        }

        function disagreePrivacy() {
            // Attempt to close tab (may be blocked by browser)
            window.open('', '_self');
            window.close();

            // Fallback: redirect to blank page
            setTimeout(() => {
                window.location.href = 'about:blank';
            }, 300);
        }
    </script>

    {{-- ================= ANIMATIONS ================= --}}
    <style>
        .animate-fade-in { opacity: 0; animation: fadeIn 0.8s forwards; }
        .animate-fade-in.delay-200 { animation-delay: 0.2s; }
        .animate-fade-in.delay-400 { animation-delay: 0.4s; }
        .animate-fade-in.delay-600 { animation-delay: 0.6s; }
        .animate-fade-in.delay-800 { animation-delay: 0.8s; }

        @keyframes fadeIn {
            to { opacity: 1; }
        }
    </style>

</body>
</html>
