<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js for modal functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); }
        .btn-nba { background: linear-gradient(to right, #e63946, #f1faee); color: white; font-weight: 600; transition: all 0.3s ease; }
        .btn-nba:hover { background: linear-gradient(to right, #d62828, #f1faee); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.3); }
        .input-nba { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; }
        .input-nba::placeholder { color: rgba(255,255,255,0.7); }
        .overlay-dark { background: rgba(0,0,0,0.5); }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="relative min-h-screen flex flex-col justify-center items-center bg-gray-900"
      x-data="accountRequests()">

    {{-- Background --}}
    <div class="absolute inset-0"
         style="background-image: url('{{ $welcomeBg ? asset('storage/' . $welcomeBg->value) : asset('images/cavraa.jpg') }}');
                background-size: cover; background-position: center;">
        <div class="absolute inset-0 overlay-dark backdrop-blur-sm"></div>
    </div>

    {{-- INFO BOX --}}
    <div class="max-w-md w-full mb-4 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded shadow-sm relative z-10">
        <h2 class="font-semibold mb-2 text-lg">Login Instructions & Data Privacy Notice</h2>
        <ul class="list-disc list-inside text-sm space-y-1">
            <li>Please enter your registered <strong>Email</strong> and <strong>Password</strong>.</li>
            <li>Your data is encrypted and protected.</li>
            <li>Contact the administrator if you encounter issues.</li>
        </ul>
    </div>

    {{-- LOGIN CARD --}}
    <div class="max-w-md w-full p-8 rounded-xl shadow-xl glass-card relative z-10">
        <h1 class="text-3xl font-bold mb-6 text-center text-white">
            {{ config('app.name') }} Login
        </h1>

        {{-- ERRORS --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-800/50 text-red-100 p-3 rounded-lg border border-red-600">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-white font-semibold mb-1">Email</label>
                <input type="email" name="email" required autofocus
                       class="input-nba block w-full p-3 rounded-md"
                       placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-white font-semibold mb-1">Password</label>
                <input type="password" name="password" required
                       class="input-nba block w-full p-3 rounded-md"
                       placeholder="••••••••">
            </div>

            <div class="flex justify-between text-sm text-white">
                <a href="#" @click.prevent="openModal('forgot_password')" class="text-blue-400 hover:underline">
                    Forgot password?
                </a>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-md bg-green-600 hover:bg-green-700 text-lg text-white font-semibold">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-white/80">
            Don’t have an account?
            <a href="#" @click.prevent="openModal('new_account')" class="text-blue-400 hover:underline font-semibold">
                Request Account
            </a>
        </p>
    </div>

    {{-- MODAL --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-2" x-text="modalTitle"></h2>
            <p class="text-gray-600 mb-4" x-text="modalMessage"></p>

            <form @submit.prevent="submitRequest">
                <input type="email" x-model="email" required
                       placeholder="Enter your email"
                       class="w-full border border-gray-300 rounded px-3 py-2 mb-3">

                <textarea x-model="message" rows="3"
                          placeholder="Enter your message (optional)"
                          class="w-full border border-gray-300 rounded px-3 py-2 mb-4"></textarea>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showModal=false"
                            class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ALPINE SCRIPT --}}
    <script>
        function accountRequests() {
            return {
                showModal: false,
                modalType: '',
                modalTitle: '',
                modalMessage: '',
                email: '',
                message: '',

                openModal(type) {
                    this.modalType = type;
                    this.email = '';
                    this.message = '';

                    if (type === 'forgot_password') {
                        this.modalTitle = 'Password Reset Request';
                        this.modalMessage = 'Please enter your email and reason for password reset.';
                    } else {
                        this.modalTitle = 'Account Request';
                        this.modalMessage = 'Please enter your email and request message.';
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
                        this.modalTitle = 'Request Submitted';
                        this.modalMessage = data.message;
                        this.email = '';
                        this.message = '';
                    })
                    .catch(() => {
                        this.modalTitle = 'Error';
                        this.modalMessage = 'Something went wrong. Please try again.';
                    });
                }
            }
        }
    </script>
</body>
</html>
