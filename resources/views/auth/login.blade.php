<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); }
        .btn-nba { background: linear-gradient(to right, #e63946, #f1faee); color: white; font-weight: 600; transition: all 0.3s ease; }
        .btn-nba:hover { background: linear-gradient(to right, #d62828, #f1faee); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.3); }
        .input-nba { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; }
        .input-nba::placeholder { color: rgba(255,255,255,0.7); }
        .overlay-dark { background: rgba(0,0,0,0.5); }
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

    {{-- Login Form Card --}}
    <div class="max-w-md w-full p-8 rounded-xl shadow-xl glass-card relative z-10">
        <h1 class="text-3xl font-bold mb-6 text-center text-white drop-shadow-lg" style="font-family: 'Poppins', sans-serif;">
            {{ config('app.name') }} Login
        </h1>

        {{-- Errors --}}
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
                <label for="email" class="block text-white font-semibold mb-1" style="font-family: 'Poppins', sans-serif;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="you@example.com"
                    class="input-nba block w-full p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="password" class="block text-white font-semibold mb-1" style="font-family: 'Poppins', sans-serif;">Password</label>
                <input id="password" type="password" name="password" required
                    placeholder="••••••••"
                    class="input-nba block w-full p-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-center justify-between text-white text-sm">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    <span class="ml-2">Remember me</span>
                </label>
                <a href="#" @click.prevent="openModal('forgot_password')" class="hover:underline text-blue-400">Forgot password?</a>
            </div>

            <button type="submit" class="w-full py-3 rounded-md bg-green-600 hover:bg-green-700 text-lg text-white hover:text-gray-300 font-semibold" style="font-family: 'Poppins', sans-serif;">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-white/80">
            Don't have an account? 
            <a href="#" @click.prevent="openModal('new_account')" class="text-blue-400 hover:underline font-semibold">Request Account</a>
        </p>
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-4" x-text="modalTitle"></h2>
            <p class="mb-4" x-text="modalMessage"></p>
            <form @submit.prevent="submitRequest">
                <input type="email" x-model="email" placeholder="Enter your email" required
                       class="w-full border border-gray-300 rounded px-3 py-2 mb-4">
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Submit
                    </button>
                    <button type="button" @click="showModal=false" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function accountRequests() {
        return {
            showModal: false,
            modalType: '',
            modalTitle: '',
            modalMessage: '',
            email: '',

            openModal(type) {
                this.modalType = type;
                this.email = '';
                if (type === 'forgot_password') {
                    this.modalTitle = 'Password Reset Request';
                    this.modalMessage = 'Enter your email to request a password reset:';
                } else if (type === 'new_account') {
                    this.modalTitle = 'Account Request';
                    this.modalMessage = 'Enter your email to request a new account:';
                }
                this.showModal = true;
            },

            submitRequest() {
                if (!this.email) return;

                fetch("{{ route('account-request.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: this.email, type: this.modalType })
                })
                .then(res => res.json())
                .then(data => {
                    this.modalTitle = this.modalType === 'forgot_password' ? 'Password Reset Request Sent' : 'Account Request Sent';
                    this.modalMessage = data.message;
                    this.showModal = true;
                })
                .catch(err => {
                    this.modalTitle = 'Error';
                    this.modalMessage = 'Something went wrong. Please try again.';
                    this.showModal = true;
                    console.error(err);
                });
            }
        }
    }
    </script>

</body>
</html>
