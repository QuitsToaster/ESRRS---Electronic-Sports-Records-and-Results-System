<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sport-card {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sport-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.2s ease;
        }
        .sport-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ef4444;
            outline: none;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3);
        }
        .sport-input::placeholder { color: rgba(255, 255, 255, 0.5); }
        .overlay-dark { background: rgba(0, 0, 0, 0.6); }
        [x-cloak] { display: none !important; }
        
        /* Sporty stripes */
        .sport-stripe {
            background: linear-gradient(45deg, transparent 30%, rgba(239, 68, 68, 0.3) 50%, transparent 70%);
        }
    </style>
</head>

<body class="relative min-h-screen flex items-center justify-center bg-gray-900"
      x-data="accountRequests()">

    {{-- BACKGROUND --}}
    <div class="absolute inset-0"
         style="background-image: url('{{ $welcomeBg ? asset('storage/' . $welcomeBg->value) : asset('images/cavraa.jpg') }}');
                background-size: cover; background-position: center;">
        <div class="absolute inset-0 overlay-dark"></div>
    </div>

    {{-- MAIN CONTAINER --}}
    <div class="relative z-10 w-full max-w-5xl px-4">
        <div class="sport-card rounded-3xl shadow-2xl overflow-hidden border border-white/10">

            <div class="grid grid-cols-1 md:grid-cols-2 min-h-[550px]">

                {{-- LEFT: SPORTY INSTRUCTION PANEL --}}
                <div class="relative p-8 bg-gradient-to-br from-red-600/90 to-red-800/90 flex flex-col justify-between">
                    {{-- Sport stripes overlay --}}
                    <div class="absolute inset-0 sport-stripe"></div>
                    
                    <div class="relative z-10">
                        {{-- Sport icon --}}
                        <div class="mb-6">
                            <svg class="w-16 h-16 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        
                        <h2 class="text-3xl font-black text-white mb-2">READY TO<br>COMPETE?</h2>
                        <div class="w-20 h-1 bg-yellow-400 mb-6"></div>
                        
                        <div class="space-y-4 text-white/90">
                            <p class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center text-red-600 font-bold text-sm">1</span>
                                <span>Enter your credentials to access the arena</span>
                            </p>
                            <p class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center text-red-600 font-bold text-sm">2</span>
                                <span>Forgot password? Click link to reset and notify the admin</span>
                            </p>
                            <p class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center text-red-600 font-bold text-sm">3</span>
                                <span>New here? Request an account</span>
                            </p>
                        </div>
                    </div>

                    {{-- Motivational quote --}}
                    <div class="relative z-10 mt-8">
                        <p class="text-white/80 text-sm italic">
                            "Victory is in the details. Log in and make your mark."
                        </p>
                    </div>
                </div>

                {{-- RIGHT: LOGIN FORM --}}
                <div class="p-8 bg-black/60 backdrop-blur-sm flex flex-col justify-center">
                    {{-- Header --}}
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-black text-white mb-1">eSRRS</h1>
                        <p class="text-red-500 font-semibold text-sm tracking-wider">ELECTRONIC SPORTS RECORDS & RESULTS SYSTEM</p>
                        <div class="w-12 h-1 bg-red-600 mx-auto mt-3"></div>
                    </div>
                    
                    {{-- ERRORS --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-red-200">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        {{-- Email Field --}}
                        <div>
                            <label class="block text-white/80 text-xs font-semibold uppercase tracking-wider mb-2">
                                Email Address
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="sport-input block w-full px-4 py-3 rounded-xl"
                                   placeholder="athlete@example.com">
                        </div>

                        {{-- Password Field --}}
                        <div>
                            <label class="block text-white/80 text-xs font-semibold uppercase tracking-wider mb-2">
                                Password
                            </label>
                            <input type="password" name="password" required
                                   class="sport-input block w-full px-4 py-3 rounded-xl"
                                   placeholder="••••••••">
                        </div>

                        {{-- Forgot Password Link --}}
                        <div class="flex justify-end">
                            <a href="#" @click.prevent="openModal('forgot_password')"
                               class="text-xs text-white/60 hover:text-red-500 transition-colors">
                                Forgot password?
                            </a>
                        </div>

                        {{-- Login Button --}}
                        <button type="submit"
                                class="w-full py-3 px-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                ENTER THE GAME
                            </span>
                        </button>

                        {{-- Request Account --}}
                        <p class="text-center text-white/50 text-sm">
                            New to eSRRS? 
                            <a href="#" @click.prevent="openModal('new_account')"
                               class="text-red-500 hover:text-red-400 font-semibold">
                                Join now
                            </a>
                        </p>
                    </form>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-white/40 text-xs mt-6">
            © {{ date('Y') }} {{ config('app.name') }}. Where champions are made.
        </p>
    </div>

    {{-- REQUEST MODAL (sporty style) --}}
    <div x-show="showModal" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div @click.away="showModal = false"
             class="bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-red-600/30">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white" x-text="modalTitle"></h2>
                <button @click="showModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="text-gray-400 mb-6" x-text="modalMessage"></p>

            <form @submit.prevent="submitRequest">
                <div class="mb-4">
                    <input type="email" x-model="email" required
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                           placeholder="Enter your email">
                </div>

                <div class="mb-6">
                    <textarea x-model="message" rows="3"
                              class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none"
                              placeholder="Enter your message (optional)"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModal=false"
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="showSuccess" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center border border-green-600/30">
            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Success!</h2>
            <p class="text-gray-400 mb-6" x-text="successMessage"></p>
            <button @click="showSuccess=false"
                    class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                Got it
            </button>
        </div>
    </div>

    {{-- ALPINE SCRIPT --}}
    <script>
        function accountRequests() {
            return {
                showModal: false,
                showSuccess: false,
                modalType: '',
                modalTitle: '',
                modalMessage: '',
                successMessage: '',
                email: '',
                message: '',

                openModal(type) {
                    this.modalType = type;
                    this.email = '';
                    this.message = '';

                    if (type === 'forgot_password') {
                        this.modalTitle = 'Password Reset';
                        this.modalMessage = 'Enter your email to reset your password.';
                    } else {
                        this.modalTitle = 'Join Us';
                        this.modalMessage = 'Enter your email and a brief message about your request.';
                    }

                    this.showModal = true;
                },

                submitRequest() {
                    fetch("{{ route('account-request.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            email: this.email,
                            message: this.message,
                            type: this.modalType
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.showModal = false;
                        this.successMessage = data.message || 'Request submitted successfully!';
                        this.showSuccess = true;
                    })
                    .catch(() => {
                        this.showModal = false;
                        this.successMessage = 'Something went wrong. Please try again.';
                        this.showSuccess = true;
                    });
                }
            }
        }
    </script>

</body>
</html>