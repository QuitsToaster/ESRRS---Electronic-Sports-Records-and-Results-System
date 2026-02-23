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
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .input-nba {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
        }
        .input-nba::placeholder { color: rgba(255,255,255,0.7); }
        .overlay-dark { background: rgba(0,0,0,0.5); }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="relative min-h-screen flex items-center justify-center bg-gray-900"
      x-data="accountRequests()">

    {{-- BACKGROUND --}}
    <div class="absolute inset-0"
         style="background-image: url('{{ $welcomeBg ? asset('storage/' . $welcomeBg->value) : asset('images/cavraa.jpg') }}');
                background-size: cover; background-position: center;">
        <div class="absolute inset-0 overlay-dark backdrop-blur-sm"></div>
    </div>

    {{-- MAIN CONTAINER --}}
    <div class="relative z-10 w-full max-w-5xl px-4">
        <div class="glass-card rounded-2xl shadow-2xl overflow-hidden">

            <div class="grid grid-cols-1 md:grid-cols-2">

                {{-- LEFT: INFO BOX --}}
                <div class="p-8 bg-blue-50 text-blue-900">
                    <h2 class="text-xl font-bold mb-4">
                        Login Instructions & Data Privacy
                    </h2>

                    <ol class="list-decimal list-inside text-sm space-y-3">
                        <li>
                            Enter your registered <strong>Email</strong> and <strong>Password</strong>.
                        </li>
                        <li>
                            Click <strong>Forgot password?</strong> if you need a password reset.
                        </li>
                        <li>
                            Click <strong>Request Account</strong> if you don’t have an account.
                        </li>
                        <li>
                            Your data is encrypted and securely protected.
                        </li>
                        <li>
                            Contact the system administrator for assistance.
                        </li>
                    </ol>
                </div>

                {{-- RIGHT: LOGIN FORM --}}
                <div class="p-8">
                    <h1 class="text-3xl font-bold mb-6 text-center text-white">
                        {{ config('app.name') }} Login
                    </h1>

                    {{-- ERRORS --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-800/60 text-red-100 p-3 rounded-lg border border-red-600">
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
                            <a href="#" @click.prevent="openModal('forgot_password')"
                               class="text-blue-400 hover:underline">
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
                        <a href="#" @click.prevent="openModal('new_account')"
                           class="text-blue-400 hover:underline font-semibold">
                            Request Account
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- REQUEST MODAL --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-2" x-text="modalTitle"></h2>
            <p class="text-gray-600 mb-4" x-text="modalMessage"></p>

            <form @submit.prevent="submitRequest">
                <input type="email" x-model="email" required
                       class="w-full border border-gray-300 rounded px-3 py-2 mb-3"
                       placeholder="Enter your email">

                <textarea x-model="message" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 mb-4"
                          placeholder="Enter your message (optional)"></textarea>

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

    {{-- SUCCESS MODAL --}}
    <div x-show="showSuccess" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 text-center">
            <div class="text-green-600 text-4xl mb-3">✔</div>
            <h2 class="text-xl font-bold mb-2">Success!</h2>
            <p class="text-gray-600 mb-4" x-text="successMessage"></p>

            <button @click="showSuccess=false"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded w-full">
                OK
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
                        this.showModal = false;
                        this.successMessage = data.message || 'Request submitted successfully.';
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
