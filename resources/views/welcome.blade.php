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

    {{-- Background Image & Overlay --}}
    <div class="relative min-h-screen flex flex-col justify-center items-center px-4"
         style="background-image: url('{{ asset('images/cavraa.jpg') }}'); background-size: cover; background-position: center;">

        {{-- Overlay for blur and darkening --}}
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

        {{-- Content --}}
        <div class="relative max-w-3xl w-full text-center z-10">

            {{-- Logo / App Name --}}
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">
                {{ config('app.name', 'CAVRAA Monitoring System') }}
            </h1>

            {{-- Tagline / Intro --}}
            <p class="text-gray-200 mb-8 text-lg md:text-xl animate-fade-in delay-200">
                Monitor, manage, and track your school sports events in real-time.
            </p>

            {{-- Buttons --}}
            <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-4 animate-fade-in delay-400">
                {{-- Admin Login --}}
                <a href="{{ route('login') }}" 
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Admin Login
                </a>

                {{-- Viewer Access --}}
                <a href="{{ route('viewer.home') }}" 
                   class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Viewer Access
                </a>

                {{-- Check Attendance --}}
                <a href="{{ route('viewer.attendance') }}" 
                   class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Check Attendance
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="mt-12 text-gray-300 text-sm text-center z-10 relative animate-fade-in delay-800">
            © {{ date('Y') }} CAVRAA Monitoring System - SDO Santiago City
        </footer>
    </div>

    {{-- Tailwind Animations --}}
    <style>
        .animate-fade-in { opacity: 0; animation: fadeIn 1s forwards; }
        .animate-fade-in.delay-200 { animation-delay: 0.2s; }
        .animate-fade-in.delay-400 { animation-delay: 0.4s; }
        .animate-fade-in.delay-600 { animation-delay: 0.6s; }
        .animate-fade-in.delay-800 { animation-delay: 0.8s; }

        @keyframes fadeIn { to { opacity: 1; } }
    </style>
</body>
</html>
