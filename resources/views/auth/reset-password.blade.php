<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email - LAPOR KOMINFO</title>
    @vite('resources/css/app.css')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 py-10 font-sans">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md p-8 text-center">

        {{-- Logo --}}
        <div class="mb-6">
            <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo KOMINFO"
                class="w-20 h-20 mx-auto object-contain">
        </div>

        {{-- Judul --}}
        <h2 class="text-gray-800 text-2xl font-bold mb-2">Atur Ulang Kata Sandi</h2>

        {{-- Deskripsi --}}
        <p class="text-gray-600 text-base leading-relaxed mb-6">
            Silakan masukkan email Anda dan kata sandi baru untuk mengatur ulang akun Anda.
        </p>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                            class="flex-shrink-0 ml-3 text-green-400 hover:text-green-600 transition-colors duration-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errors->any())
            <div id="error-box" class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-sm">
                <!-- Judul "ERRORS!" -->
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg mr-2"></i>
                    <p class="text-sm font-semibold text-red-800">ERRORS!</p>
                </div>

                <!-- List Error -->
                <div class="flex items-start">
                    <div class="ml-3 flex-1">
                        <ul class="space-y-1 text-sm text-red-700 text-justify">
                            @foreach ($errors->all() as $error)
                                <li class="flex gap-2">
                                    <span>•</span>
                                    <span class="flex-1">{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Tombol close -->
                    <button type="button"
                        onclick="document.getElementById('error-box').style.display='none'"
                        class="ml-3 text-red-400 hover:text-red-600 transition-colors duration-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Form Reset Password -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-3 text-left">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" required readonly
                    value="{{ old('email', $email ?? '') }}"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                    <i class="fas fa-lock text-gray-400 mr-3"></i>
                    <input type="password" name="password" id="password" placeholder="Password"
                            class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                    <button type="button" onclick="togglePassword()" class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="toggleIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                    <i class="fas fa-lock text-gray-400 mr-3"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi Password"
                            class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                    <button type="button" onclick="toggleConfirmPassword()" class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="toggleConfirmIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition duration-200">
                <i class="fas fa-lock mr-2"></i> Atur Ulang Kata Sandi
            </button>
        </form>

        <!-- Footer -->
        <p class="text-xs text-gray-400 mt-8">
            © 2025 Dinas Komunikasi dan Informatika Kabupaten Kebumen
        </p>

    </div>

<!-- Script -->
  <script src="{{ asset('js/reset-password.js') }}"></script>
</body>
</html>



