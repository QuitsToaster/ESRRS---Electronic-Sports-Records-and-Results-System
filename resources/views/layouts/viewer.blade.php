<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Viewer Dashboard')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans" x-data="{ open: false }">

    {{-- Top Navbar --}}
    <nav class="bg-blue-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo / Brand --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('viewer.home') }}" class="text-lg font-bold">CEMS</a>
                </div>

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center space-x-6 font-medium">
                    <a href="{{ route('viewer.home') }}"
                       class="hover:text-gray-200 {{ request()->routeIs('viewer.home') ? 'underline font-semibold' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('viewer.participants') }}"
                       class="hover:text-gray-200 {{ request()->routeIs('viewer.participants') ? 'underline font-semibold' : '' }}">
                        Participants
                    </a>
                    <a href="{{ route('viewer.events') }}"
                       class="hover:text-gray-200 {{ request()->routeIs('viewer.events') ? 'underline font-semibold' : '' }}">
                        Events
                    </a>
                    <a href="{{ route('viewer.games') }}"
                       class="hover:text-gray-200 {{ request()->routeIs('viewer.games') ? 'underline font-semibold' : '' }}">
                        Games
                    </a>
                    <a href="{{ route('viewer.announcements') }}"
                       class="hover:text-gray-200 {{ request()->routeIs('viewer.announcements') ? 'underline font-semibold' : '' }}">
                        Announcements
                    </a>
                    <a href="{{ route('viewer.feedbacks') }}"
                       class="hover:text-gray-200 {{ request()->routeIs('viewer.feedbacks') ? 'underline font-semibold' : '' }}">
                        Feedbacks
                    </a>

                    {{-- Logout Button --}}
                    <a href="{{ url('/') }}" 
                       class="ml-4 px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-white font-semibold transition">
                        Logout
                    </a>
                </div>

                {{-- Mobile Menu Button --}}
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" class="md:hidden bg-blue-700 text-white space-y-1 px-2 pt-2 pb-3" x-cloak>
            <a href="{{ route('viewer.home') }}" 
               class="block px-3 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('viewer.home') ? 'font-semibold bg-blue-800' : '' }}">
                Home
            </a>
            <a href="{{ route('viewer.participants') }}" 
               class="block px-3 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('viewer.participants') ? 'font-semibold bg-blue-800' : '' }}">
                Participants
            </a>
            <a href="{{ route('viewer.events') }}" 
               class="block px-3 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('viewer.events') ? 'font-semibold bg-blue-800' : '' }}">
                Events
            </a>
            <a href="{{ route('viewer.games') }}" 
               class="block px-3 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('viewer.games') ? 'font-semibold bg-blue-800' : '' }}">
                Games
            </a>
            <a href="{{ route('viewer.announcements') }}" 
               class="block px-3 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('viewer.announcements') ? 'font-semibold bg-blue-800' : '' }}">
                Announcements
            </a>
            <a href="{{ route('viewer.feedbacks') }}" 
               class="block px-3 py-2 rounded hover:bg-blue-600 {{ request()->routeIs('viewer.feedbacks') ? 'font-semibold bg-blue-800' : '' }}">
                Feedbacks
            </a>
            {{-- Mobile Logout --}}
            <a href="{{ url('/') }}" 
               class="block px-3 py-2 mt-1 rounded bg-red-600 hover:bg-red-700 text-white font-semibold">
                Logout
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto p-4">
        @yield('content')
    </main>

</body>
</html>
