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
            <div class="flex justify-center space-x-4 animate-fade-in delay-400">
                <a href="{{ route('login') }}" 
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow-lg transition transform hover:-translate-y-1 hover:scale-105">
                    Login
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" 
                       class="px-6 py-3 bg-white hover:bg-gray-100 text-blue-600 font-semibold rounded shadow-lg border border-blue-600 transition transform hover:-translate-y-1 hover:scale-105">
                        Register
                    </a>
                @endif
            </div>

            {{-- Optional Illustration / Banner --}}
            <div class="mt-12 animate-fade-in delay-600">
                <img src="{{ asset('images/landing-banner.svg') }}" 
                     alt="Sports illustration" 
                     class="mx-auto w-full max-w-lg drop-shadow-lg">
            </div>

        </div>

        {{-- Footer --}}
        <footer class="mt-12 text-gray-300 text-sm text-center z-10 relative animate-fade-in delay-800">
            © {{ date('Y') }} CAVRAA Monitoring System - SDO Santiago City
        </footer>
    </div>

    {{-- Tailwind Animations --}}
    <style>
        .animate-fade-in {
            opacity: 0;
            animation: fadeIn 1s forwards;
        }
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
